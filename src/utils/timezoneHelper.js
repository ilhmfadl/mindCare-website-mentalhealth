// Helper untuk menangani zona waktu di client-side
export class TimezoneHelper {
  // Mendapatkan zona waktu client
  static getClientTimezone() {
    try {
      return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
      console.warn('Could not get timezone, using Asia/Jakarta as fallback');
      return 'Asia/Jakarta';
    }
  }

  // Mendapatkan waktu client saat ini
  static getCurrentTime() {
    const now = new Date();
    return now.toISOString().slice(0, 19).replace('T', ' ');
  }

  // Format waktu untuk display (hanya jam dan menit dengan AM/PM)
  static formatTimeForDisplay(utcTime, timezone = null) {
    if (!utcTime) return '';
    
    try {
      const tz = timezone || this.getClientTimezone();
      const date = new Date(utcTime + 'Z'); // Add Z to treat as UTC
      return date.toLocaleTimeString('en-US', {
        timeZone: tz,
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    } catch (e) {
      console.warn('Time formatting failed:', e);
      return utcTime;
    }
  }

  // Format waktu relatif (seperti "2 jam yang lalu")
  static formatRelativeTime(utcTime, timezone = null) {
    if (!utcTime) return '';
    
    try {
      const tz = timezone || this.getClientTimezone();
      const date = new Date(utcTime + 'Z');
      const now = new Date();
      
      // Convert both to the same timezone for comparison
      const dateInTz = new Date(date.toLocaleString('en-US', { timeZone: tz }));
      const nowInTz = new Date(now.toLocaleString('en-US', { timeZone: tz }));
      
      const diffMs = nowInTz - dateInTz;
      const diffMinutes = Math.floor(diffMs / (1000 * 60));
      const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
      const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
      
      if (diffMinutes < 1) {
        return 'Baru saja';
      } else if (diffMinutes < 60) {
        return `${diffMinutes} menit yang lalu`;
      } else if (diffHours < 24) {
        return `${diffHours} jam yang lalu`;
      } else if (diffDays === 1) {
        return `Kemarin ${date.toLocaleTimeString('en-US', { 
          timeZone: tz, 
          hour: '2-digit', 
          minute: '2-digit',
          hour12: true
        })}`;
      } else {
        return date.toLocaleDateString('id-ID', { 
          timeZone: tz,
          year: 'numeric',
          month: '2-digit',
          day: '2-digit'
        }) + ' ' + date.toLocaleTimeString('en-US', {
          timeZone: tz,
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        });
      }
    } catch (e) {
      console.warn('Relative time formatting failed:', e);
      return utcTime;
    }
  }

  // Menambahkan parameter zona waktu ke request
  static addTimezoneToRequest(data = {}) {
    return {
      ...data,
      current_time: this.getCurrentTime(),
      timezone: this.getClientTimezone()
    };
  }

  // Menambahkan parameter zona waktu ke URL
  static addTimezoneToUrl(url) {
    const separator = url.includes('?') ? '&' : '?';
    return `${url}${separator}timezone=${encodeURIComponent(this.getClientTimezone())}`;
  }
}

// Export default untuk backward compatibility
export default TimezoneHelper; 