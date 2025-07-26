<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Only POST method is allowed");
    }
    
    // Get POST data
    $response_id = isset($_POST['response_id']) ? (int)$_POST['response_id'] : 0;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $vote_type = isset($_POST['vote_type']) ? $_POST['vote_type'] : 'upvote'; // upvote or downvote
    
    // Validation
    if (!$response_id) {
        throw new Exception("Response ID is required");
    }
    
    if (!$user_id) {
        throw new Exception("User ID is required");
    }
    
    if (!in_array($vote_type, ['upvote', 'downvote'])) {
        throw new Exception("Invalid vote type");
    }
    
    // Check if response exists
    $checkQuery = "SELECT id, vote FROM forum_responses WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $checkStmt->bind_param('i', $response_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception("Respon tidak ditemukan");
    }
    
    $responseData = $checkResult->fetch_assoc();
    $currentVote = (int)$responseData['vote'];
    $checkStmt->close();
    
    // Check if user already voted on this response
    $voteCheckQuery = "SELECT id, vote_type FROM forum_response_votes WHERE user_id = ? AND response_id = ?";
    $voteCheckStmt = $conn->prepare($voteCheckQuery);
    if (!$voteCheckStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $voteCheckStmt->bind_param('ii', $user_id, $response_id);
    $voteCheckStmt->execute();
    $voteCheckResult = $voteCheckStmt->get_result();
    
    $newVote = $currentVote;
    $voteChange = 0;
    $action = '';
    
    if ($voteCheckResult->num_rows > 0) {
        // User already voted
        $existingVote = $voteCheckResult->fetch_assoc();
        
        if ($existingVote['vote_type'] === $vote_type) {
            // User is voting the same way again - remove the vote
            $deleteVoteQuery = "DELETE FROM forum_response_votes WHERE user_id = ? AND response_id = ?";
            $deleteVoteStmt = $conn->prepare($deleteVoteQuery);
            $deleteVoteStmt->bind_param('ii', $user_id, $response_id);
            $deleteVoteStmt->execute();
            $deleteVoteStmt->close();
            
            // Decrease vote count
            $voteChange = ($vote_type === 'upvote') ? -1 : 1;
            $newVote = $currentVote + $voteChange;
            $action = 'removed';
        } else {
            // User is changing vote type
            $updateVoteQuery = "UPDATE forum_response_votes SET vote_type = ? WHERE user_id = ? AND response_id = ?";
            $updateVoteStmt = $conn->prepare($updateVoteQuery);
            $updateVoteStmt->bind_param('sii', $vote_type, $user_id, $response_id);
            $updateVoteStmt->execute();
            $updateVoteStmt->close();
            
            // Calculate vote change (from old type to new type)
            $voteChange = ($vote_type === 'upvote') ? 2 : -2; // +2 for upvote, -2 for downvote
            $newVote = $currentVote + $voteChange;
            $action = 'changed';
        }
    } else {
        // User hasn't voted yet - add new vote
        $insertVoteQuery = "INSERT INTO forum_response_votes (user_id, response_id, vote_type) VALUES (?, ?, ?)";
        $insertVoteStmt = $conn->prepare($insertVoteQuery);
        $insertVoteStmt->bind_param('iis', $user_id, $response_id, $vote_type);
        $insertVoteStmt->execute();
        $insertVoteStmt->close();
        
        // Increase vote count
        $voteChange = ($vote_type === 'upvote') ? 1 : -1;
        $newVote = $currentVote + $voteChange;
        $action = 'added';
    }
    
    $voteCheckStmt->close();
    
    // Update response vote count
    $updateQuery = "UPDATE forum_responses SET vote = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    if (!$updateStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $updateStmt->bind_param('ii', $newVote, $response_id);
    
    if ($updateStmt->execute()) {
        $updateStmt->close();
        $conn->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Vote berhasil ' . $action,
            'data' => [
                'response_id' => $response_id,
                'old_vote' => $currentVote,
                'new_vote' => $newVote,
                'vote_change' => $voteChange,
                'action' => $action,
                'vote_type' => $vote_type
            ]
        ]);
    } else {
        throw new Exception("Failed to update vote: " . $updateStmt->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 