package com.sssa.slrtce.misc.utils;

/**
 * Created by Coolalien on 2/17/2017.
 */

public class Constants {

    /**
     * Permission
     */
    public static final int PERMISSIONS_REQ = 1;
    
    /*making the ip address a single declaration for easy modification
     * you can also add the port your wamp or xamp is listening to
     * if it is not using the default e.g mine is listening to port 8080
     * i.e 192.168.1.2:8080
     */
    private static String ipAdd = "http://192.168.1.2";

    

    /**
     * Server Links
     */
<<<<<<< HEAD
    public static final String SRegisterUrl = "http://192.168.0.102/demo/register.php";
    public static final String SLoginUrl = "http://192.168.0.102/demo/login.php";

    public static final String TRegisterUrl = "http://192.168.0.102/demo/otherregister.php";
    public static final String TLoginUrl = "http://192.168.0.102/demo/otherlogin.php";

    public static final String NTegisterUrl = "http://192.168.0.102/demo/nontregister.php";
    public static final String NTLoginUrl = "http://192.168.0.102/demo/nontlogin.php";

    public static final String SYLLABUSDATA = "http://192.168.0.102/demo/syllabus/syllabusTeacher.php";

    public static final String SAVINGSYLLABUS = "http://192.168.0.102/demo/syllabus/saveSyllabus.php";
    public static final String UNSAVINGSYLLABUS = "http://192.168.0.102/demo/syllabus/unsaveSyllabus.php";

    public static final String LOADSYLLABUS = "http://192.168.0.102/demo/syllabus/dataStudent.php";

    public static final String UPLOADFILES = "http://192.168.0.102/demo/upload.php";
    public static final String CMPNUPLOAD = "http://192.168.0.102/demo/cmpnupload.php";
    public static final String ITUPLOAD = "http://192.168.0.102/demo/itupload.php";
    public static final String EXTCUPLOAD = "http://192.168.0.102/demo/extcupload.php";
    public static final String ETRXUPLOAD = "http://192.168.0.102/demo/etrxupload.php";
    public static final String MECHUPLOAD = "http://192.168.0.102/demo/mechupload.php";
    public static final String CIVILUPLOAD = "http://192.168.0.102/demo/civilupload.php";

    public static final String UPLOADFILESG = "http://192.168.0.102/demo/gupload.php";

    public static final String FILEVIEWS = "http://192.168.0.102/demo/getuploaded.php";
    public static final String FILEVIEWS2 = "http://192.168.0.102/demo/getuploadedg.php";

    public static final String FORGOTPASS = "http://192.168.0.102/demo/forgotpassword.php";

    public static final String FORGOTPASST = "http://192.168.0.102/demo/forgotpasswordt.php";

    public static final String FORGOTPASSNT = "http://192.168.0.102/demo/forgotpasswordnt.php";


    public static final String TAKEATTENDANCE = "http://192.168.0.102/demo/attendance/insertAttendance.php";
    
    public static final String VIEWATTENDANCE = "http://192.168.0.102/demo/attendance/viewAttendance.php";
=======
    public static final String SRegisterUrl = ipAdd + "/demo/register.php";
    public static final String SLoginUrl = ipAdd + "/demo/login.php";

    public static final String TRegisterUrl = ipAdd + "/demo/otherregister.php";
    public static final String TLoginUrl = ipAdd + "/demo/otherlogin.php";

    public static final String NTegisterUrl = ipAdd + "/demo/nontregister.php";
    public static final String NTLoginUrl = ipAdd + "/demo/nontlogin.php";

    public static final String SYLLABUSDATA = ipAdd + "/demo/syllabus/syllabusTeacher.php";

    public static final String SAVINGSYLLABUS = ipAdd + "/demo/syllabus/saveSyllabus.php";
    public static final String UNSAVINGSYLLABUS = ipAdd + "/demo/syllabus/unsaveSyllabus.php";

    public static final String LOADSYLLABUS = ipAdd + "/demo/syllabus/dataStudent.php";

    public static final String UPLOADFILES = ipAdd + "/demo/upload.php";
    public static final String CMPNUPLOAD = ipAdd + "/demo/cmpnupload.php";
    public static final String ITUPLOAD = ipAdd + "/demo/itupload.php";
    public static final String EXTCUPLOAD = ipAdd + "/demo/extcupload.php";
    public static final String ETRXUPLOAD = ipAdd + "/demo/etrxupload.php";
    public static final String MECHUPLOAD = ipAdd + "/demo/mechupload.php";
    public static final String CIVILUPLOAD = ipAdd + "/demo/civilupload.php";

    public static final String UPLOADFILESG = ipAdd + "/demo/gupload.php";

    public static final String FILEVIEWS = ipAdd + "/demo/getuploaded.php";
    public static final String FILEVIEWS2 = ipAdd + "/demo/getuploadedg.php";

    public static final String FORGOTPASS = ipAdd + "/demo/forgotpassword.php";

    public static final String FORGOTPASST = ipAdd + "/demo/forgotpasswordt.php";

    public static final String FORGOTPASSNT = ipAdd + "/demo/forgotpasswordnt.php";


    public static final String TAKEATTENDANCE = ipAdd + "/demo/attendance/insertAttendance.php";
    
    public static final String VIEWATTENDANCE = ipAdd + "/demo/attendance/viewAttendance.php";
>>>>>>> 4ef647afa2c3287100dc1b57c83190afb2a146a5


    /**
     * Preferences
     */
    public static final String INOUROUT = "loggedIn";
    public static final String USERNAME = "username";
    public static final String STUDENT_LOGIN = "studentlogin";
    public static final String TEACHER_LOGIN = "teacherlogin";
    public static final String NTEACHER_LOGIN = "nteacherlogin";
    public static final String STUDENT_LOGIN_TRACK = "strack";
    public static final String TEACHER_LOGIN_TRACK = "ttrack";
    public static final String NTEACHER_LOGIN_TRACK = "nttrack";
    public static final String STUDENTINIT = "branch";
    public static final String ONETIMESCREEN = "onetimescreen";
    public static final String BRANCHCMPN = "branchcmpn";
    public static final String COMMONYR = "year";
    public static final String FOURTHYR = "fourth_year";
    public static final String SUBJECTNAME = "subjectname";
    public static final String STARTDATE = "startdate";
    public static final String ENDDATE = "enddate";
    public static final String ATDATA = "atdata";
    public static final String COUNTER = "counter";
    public static final String SYLLABUS = "syllabus";
    public static final String NOTICETRACK = "noticeTrack";


    /**
     * Extras
     */
    public static final String DATE_FORMAT_NOW = "yyyy-MM-dd HH:mm:ss";

}
