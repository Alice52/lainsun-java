import java.io.IOException;
import java.text.ParseException;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;

import org.json.JSONException;

public final class UTCTimeUtil {
    
    /**
     * util class should not be initial
     */
    private UTCTimeUtil() {
    }

    /**
     * Get calendar that time zone is UTC
     * 
     * @param date
     * @return
     */
    public static Calendar toUTCCalendar(Date date) {
        if (date == null) {
            return null;
        }

        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        calendar.setTimeZone(TimeZone.getTimeZone("UTC"));
        return calendar;
    }
    
    /**
     * Convert local time to utc time (time subtract local timezone offset)
     * 
     * @param date
     * @param local
     * @return
     */
    public static Date localToUtc(Date date, TimeZone local) {
        
        if (date == null || local == null) {
            return null;
        }
        
        Date utc = null;
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        calendar.add(Calendar.MILLISECOND, 0 - local.getRawOffset() - local.getDSTSavings());
        utc = calendar.getTime();
        
        return utc;
    }
    
    /**
     * Convert local time to utc time (time subtract local timezone offset)
     * 
     * @param date
     * @param local
     * @return
     */
    public static Date localToUtc(Date date) {
        
        return localToUtc(date, TimeZone.getDefault());
    }
    
    /**
     * Convert utc time to local time (time add local timezone offset)
     * @param date
     * @param local
     * @return
     */
    public static Date utcToLocal(Date date, TimeZone local) {
        
        Date localDate = null;
        if (date != null) {
            Calendar calendar = Calendar.getInstance();
            calendar.setTime(date);
            calendar.add(Calendar.MILLISECOND, local.getRawOffset() + local.getDSTSavings());
            localDate = calendar.getTime();
        }
        
        return localDate;
    }
    
    /**
     * Convert utc time to local time (time add local timezone offset)
     * @param date
     * @param local
     * @return
     */
    public static Date utcToLocal(Date date) {
        
        return utcToLocal(date, TimeZone.getDefault());
    }
}
