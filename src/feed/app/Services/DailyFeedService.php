<?php 

namespace App\Service;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DailyFeedService
{
    public function __construct()
    {
        
    } 

    public function process(Request $request)
    {
        // $date = new DateTime($request->mydate);
        // $tglSatu = $date->modify('first day of this month')->format('YYYYmmdd');
        // $tgl = $date->format('YYYYmmdd');

        // $accAcademic = DB::connection('sqlsrv')
        //                 ->table('masteracc')
        //                 ->selectRaw('SUBSTRING(\"00000000\",1,8-LEN(no_cif))+ no_cif nocif')
        //                 ->whereIn('accupation',['9','6'])
        //                 ->whereNotIn('block',['C','D'])
        //                 ->pluck('nocif')
        //                 ->implode(',');

        // $sSPM = DB::connection('sqlsrv')
        //             ->table('subacc')
        //             ->selectRaw('SUBSTRING("00000000",1,8-LEN(no_cust))+ no_cust nocust, dateadd(mm, 1, dateadd(yy,1,open_date)')
        //             ->whereIn('no_cust',function($query){
        //                 $query->select('no_cust')
        //                         ->table('sasol.dbo.spm');
        //             })
        //             ->whereRaw('datediff(mm,20231022, dateadd(mm, 1, dateadd(y,1,open_date))) > 0')
        //             ->pluck('nocust')
        //             ->implode(',');

        // $query = "SELECT TO_CHAR(CUSTOMER.THE_DATE,'yyyy-mm-dd') \"TANGGAL\", nvl(CUSTOMER.CUST,0) \"CUSTOMER\", nvl(STAFF.EMP,0) \"STAFF\", nvl(TRIAL.TEMP,0) \"TRIAL\", nvl(ACADEMIC.EDU,0) \"ACADEMIC\", nvl(SPM.SEK,0) \"SPM\",  nvl(CUSTOMER.CUST,0)+nvl(STAFF.EMP,0)+" +
        //         "nvl(TRIAL.TEMP,0)+nvl(ACADEMIC.EDU,0)+nvl(SPM.SEK,0) \"SUM\" "+
        //         "FROM  "+
        //         "(SELECT THE_DATE, COUNT(DISTINCT(LOGIN_ID))CUST "+
        //         "FROM   HLOGINLOG  "+
        //         "WHERE  THE_DATE BETWEEN TO_DATE(' "+ $tglSatu + "','YYYYMMDD') AND TO_DATE('"+$tgl+"','YYYYMMDD') "+
        //         "AND    TRIM(TYPE) = 'HTS' AND ACCT_NO NOT IN ("+ $accAcademic;
        // if (strlen($sSPM)>0) {
        //     $query += $query+','+$sSPM+')';
        // } else {
        //     $query+')';
        // }
        // $query + "GROUP BY THE_DATE) CUSTOMER, "+
        //         "(SELECT THE_DATE, COUNT(DISTINCT(LOGIN_ID)) EMP "+
        //         "FROM   HLOGINLOG  "+ 
        //         "WHERE  THE_DATE BETWEEN TO_DATE('" + $tglSatu + "' ,'YYYYMMDD') AND TO_DATE('"+$tgl+"','YYYYMMDD') "+
        //         "AND    TRIM(TYPE) = 'EMP' "+
        //         "GROUP BY THE_DATE) STAFF, "+
        //         "(SELECT THE_DATE, COUNT(DISTINCT(LOGIN_ID))TEMP "+
        //         "FROM   HLOGINLOG "+
        //         "WHERE  THE_DATE BETWEEN TO_DATE('" + $tglSatu + "','YYYYMMDD') AND TO_DATE('"+$tgl+"','YYYYMMDD') "+
        //         "AND    TRIM(TYPE) = 'TMP'  "+
        //         "GROUP BY THE_DATE) TRIAL, "+
        //         "(SELECT THE_DATE, COUNT(DISTINCT(LOGIN_ID)) EDU "+
        //         "FROM   HLOGINLOG "+
        //         "WHERE  THE_DATE BETWEEN TO_DATE('" + $tglSatu + "','YYYYMMDD') AND TO_DATE('"+$tgl+"','YYYYMMDD') "+
        //         "AND    TRIM(TYPE) = 'HTS' AND ACCT_NO IN ('"+ $accAcademic + "') "+
        //         "GROUP BY THE_DATE) ACADEMIC, "+
        //         "(SELECT THE_DATE, COUNT(DISTINCT(LOGIN_ID)) SEK "+
        //         "FROM   HLOGINLOG "+
        //         "WHERE  THE_DATE BETWEEN TO_DATE('" + $tglSatu + "','YYYYMMDD') AND TO_DATE('"+$tgl+"','YYYYMMDD') "+
        //         "AND    TRIM(TYPE) = 'HTS' AND ACCT_NO IN ("+ $sSPM + ") "+
        //         "GROUP BY THE_DATE) SPM "+
        //         "WHERE CUSTOMER.THE_DATE = STAFF.THE_DATE(+) "+
        //         "AND   CUSTOMER.THE_DATE = TRIAL.THE_DATE(+) "+
        //         "AND   CUSTOMER.THE_DATE = ACADEMIC.THE_DATE(+) "+
        //         "AND   CUSTOMER.THE_DATE = SPM.THE_DATE(+) "+
        //         "ORDER BY CUSTOMER.THE_DATE" ;
        
        // $result = DB::connection('sqlsrv')
        //         ->statement($query);

    }

}