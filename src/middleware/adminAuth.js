// Middleware untuk autentikasi admin
export function requireAdminAuth(to, from, next) {
  // Ambil data user dari localStorage
  const user = JSON.parse(localStorage.getItem('user') || 'null');
  
  // Cek apakah user ada dan memiliki role admin
  if (!user || user.role !== 'admin') {
    // Redirect ke halaman login jika bukan admin
    next('/login');
    return;
  }
  
  // Jika sudah admin, lanjutkan ke halaman yang dituju
  next();
}

// Middleware untuk cek apakah user sudah login (untuk halaman login/register)
export function requireGuest(to, from, next) {
  const user = JSON.parse(localStorage.getItem('user') || 'null');
  
  if (user) {
    // Jika sudah login, redirect berdasarkan role
    if (user.role === 'admin') {
      next('/admin/chat');
    } else {
      next('/');
    }
    return;
  }
  
  next();
} 