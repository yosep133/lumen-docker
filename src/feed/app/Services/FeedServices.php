<?php 

namespace App\Services;

use App\Events\Event;
use DateTime;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use PhpParser\ErrorHandler\Collecting;
use Predis\Command\Redis\ECHO_;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use function GuzzleHttp\Promise\each;

class FeedServices 
{
    public $count =0;
    public function __construct()
    {
        
    }

    public function allProcess(Request $request)
    {
        $varDate = $this->varDate($request->mydate);
        // lock button process 
        $this->startProgress();
        $this->porcessSatu($request); $this->detailProgress('1',$varDate);//daily transaction 
        $this->porcessDua($request); $this->detailProgress('2',$varDate);//monthly transaction
        $this->processDailyTrans($request); $this->detailProgress('3',$varDate);
        $this->processBranchTrans($request); $this->detailProgress('4',$varDate);
        $this->processMobileTrans($request); $this->detailProgress('5',$varDate);
        $this->processClientTrans($request); $this->detailProgress('6',$varDate);
        $this->processDetailTrans($request); $this->detailProgress('7',$varDate);
        $this->processNegoTrans($request); $this->detailProgress('8',$varDate);
        $this->processKomisiInterest($request); $this->detailProgress('9',$varDate);
        $this->processClientName($request); $this->detailProgress('10',$varDate);
        // alert process selesai
        $this->endProgress($varDate);  

    }

    function varDate($mydate){
        $tglSatu = Carbon::parse($mydate)->startOfMonth();
        $tgl = Carbon::createFromFormat('Ymd',$mydate);
        $tglmysql = $tgl->format('Y-m-d');
        $bln = $tgl->month;
        $thn = $tgl->year;
        return [
                'tglsatu'=> $tglSatu, 'tgl'=>$tgl ,'bln'=>$bln ,'thn'=>$thn, 'tglmysql'=>$tglmysql 
        ];
    }

    function porcessSatu(Request $request){
                
        echo 'Running Process Daily Transaction \n ';
        $varDate = $this->varDate($request->mydate);
        
        $tglSatu = Carbon::parse($request->mydate)->startOfMonth()->format('Ymd');
        $tglSPM = Carbon::parse($request->mydate)->format('Ymd');;
        echo ' first Day : '.$tglSatu.' now date '.$tglSPM.' mydate '.$request->mydate;
        $tglSatu = Carbon::parse($request->mydate)->startOfMonth();
        $tgl = Carbon::createFromFormat('Ymd',$request->mydate);
        echo ' first Day : '.$tglSatu.' now date '.$tgl.' mydate '.$request->mydate;
        $finishValue = 100;

        $accAcademic = DB::connection('sqlsrv')
                        ->table('masteracc')
                        ->selectRaw('SUBSTRING(\'00000000\',1,8-LEN(no_cif))+ no_cif nocif')
                        ->whereIn('occupation',['9','6'])
                        ->whereNotIn('block',['C','D'])
                        ->pluck('nocif')
                        // ->implode(',')
                        // ->get()
                        ;

        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $sSPM = DB::connection('sqlsrv')
                    ->table('subacc')
                    ->selectRaw('SUBSTRING(\'00000000\',1,8-LEN(no_cust))+ no_cust nocust')
                    ->whereIn('no_cust',function($query){
                        $query->select('no_cust')
                            ->from('sasol.dbo.spm');
                    })
                    ->whereRaw('datediff(mm,\''.$tglSPM.'\', dateadd(mm, 1, dateadd(yy,1,open_date))) > 0')
                    ->pluck('nocif')
                    // ->implode(',')
                    // ->get()
                    ;
        // echo ' subQCustomer '.$sSPM->count()>0?$accAcademic->push($sSPM)->implode(','):$accAcademic->implode(',');

        if ($accAcademic->count()>1000) {
            $rawQuery = '(ACCT_NO not in ('.($sSPM->count()>0?$accAcademic->slice(0,500)->push($sSPM)->implode(','):$accAcademic->slice(0,500)->implode(',')).')
                        OR ACCT_NO not in ('.($sSPM->count()>0?$accAcademic->slice(500,$accAcademic->count())->push($sSPM)->implode(','):$accAcademic->slice(500,$accAcademic->count())->implode(',')).')
                        )';
            $rawAcademic='(ACCT_NO IN ('.$accAcademic->slice(0,500)->implode(',').') 
                        OR ACCT_NO IN ('.$accAcademic->slice(500,$accAcademic->count())->implode(',').') 
                        )';
        } else {
            $rawQuery = 'ACCT_NO not in ('.($sSPM->count()>0?$accAcademic->push($sSPM)->implode(','):$accAcademic->implode(',')).')';
            $rawAcademic='(ACCT_NO IN ('.$accAcademic->slice(0,500)->implode(',').')';
        }

        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 15
        ];
        $this->loadingProgress($param);

        $subQCustomer   = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('TRUNC(LOGIN_DATE) LOGIN_DATE,COUNT(DISTINCT(LOGIN_ID)) CUST')
                            ->join('MA001A02','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween(DB::raw('TRUNC(LOGIN_DATE)'),[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(USER_TP)'),'=','0')
                            ->whereRaw($rawQuery)
                            ->groupBy(DB::raw('TRUNC(LOGIN_DATE)'))
                            // ->get()
                            ;

        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 20
        ];
        $this->loadingProgress($param);

        $subQStaff      = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('TRUNC(LOGIN_DATE) LOGIN_DATE, COUNT(DISTINCT(CUST_ID)) EMP')
                            ->whereBetween(DB::raw('TRUNC(LOGIN_DATE)'),[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(USER_TP)'),'=','1')
                            ->groupBy(DB::raw('TRUNC(LOGIN_DATE)'))
                            // ->get()
                            ;
                            
        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 22
        ];
        $this->loadingProgress($param);

        $subQTrial      = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('TRUNC(LOGIN_DATE) LOGIN_DATE, COUNT(DISTINCT(CUST_ID)) TEMP')
                            ->join('MA001A11','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween(DB::raw('TRUNC(LOGIN_DATE)'),[$tglSatu,$tgl])
                            // ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            ->groupBy(DB::raw('TRUNC(LOGIN_DATE)'))
                            // ->get()
                            ;
        
        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 24
        ];
        $this->loadingProgress($param);

        $subQAcademic   = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('TRUNC(LOGIN_DATE) LOGIN_DATE, COUNT(DISTINCT(CUST_ID)) EDU')
                            ->join('MA001A02','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween(DB::raw('TRUNC(LOGIN_DATE)'),[$tglSatu,$tgl])
                            // ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            ->whereRaw($rawAcademic)
                            ->groupBy(DB::raw('TRUNC(LOGIN_DATE)'))
                            // ->get()
                            ;
                            
        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 26
        ];
        $this->loadingProgress($param);

        $subQSPM        = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('TRUNC(LOGIN_DATE) LOGIN_DATE, COUNT(DISTINCT(CUST_ID))SEK')
                            ->join('MA001A02','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween(DB::raw('TRUNC(LOGIN_DATE)'),[$tglSatu,$tgl])
                            // ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            ->whereRaw('ACCT_NO IN ('.($sSPM->count()>0?$sSPM->implode(','):'\'\'').')')
                            ->groupBy(DB::raw('TRUNC(LOGIN_DATE)'))
                            // ->get()
                            ;
        

        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 28
        ];
        $this->loadingProgress($param);
     
        $dateFeed       = DB::connection('oracle')->query()
                            ->fromSub($subQCustomer,'CUSTOMER')
                            ->selectRaw('TO_CHAR(CUSTOMER.LOGIN_DATE,\'yyyy-mm-dd\') TANGGAL')
                            ->selectRaw('nvl(CUSTOMER.CUST,0) "CUSTOMER"')
                            ->selectRaw('nvl(STAFF.EMP,0) "STAFF"')
                            ->selectRaw('nvl(TRIAL.TEMP,0) "TRIAL"')
                            ->selectRaw('nvl(ACADEMIC.EDU,0) "ACADEMIC"')
                            ->selectRaw('nvl(SPM.SEK,0) "SPM"')
                            ->selectRaw('nvl(CUSTOMER.CUST,0)+nvl(STAFF.EMP,0)+
                            nvl(TRIAL.TEMP,0)+nvl(ACADEMIC.EDU,0)+nvl(SPM.SEK,0) "JUMLAH"')
                            ->joinSub($subQStaff,'STAFF','CUSTOMER.LOGIN_DATE','=','STAFF.LOGIN_DATE','full outer')
                            ->joinSub($subQTrial,'TRIAL','CUSTOMER.LOGIN_DATE','=','TRIAL.LOGIN_DATE','full outer')
                            ->joinSub($subQAcademic,'ACADEMIC','CUSTOMER.LOGIN_DATE','=','ACADEMIC.LOGIN_DATE','full outer')
                            ->joinSub($subQSPM,'SPM','CUSTOMER.LOGIN_DATE','=','SPM.LOGIN_DATE','full outer')
                            ->orderBy('CUSTOMER.LOGIN_DATE','asc')
                            ->get()
                            ;
        // echo '== '.$subQSPM->get()->toSql();

        // echo ' -- '.$dateFeed->toSql();

        // foreach ($dateFeed as $val) {
        //     echo 'foreach '.$val->tanggal.' '.$val->customer;
        // };
        
        $param = [
            'procName'=> '1',
            'finishValue' => $finishValue,
            'procValue'=> 30
        ];
        $this->loadingProgress($param);
        $finishValue = $dateFeed->count();
        $iteration = 70/$finishValue;
        $this->count = 30;
        collect($dateFeed)->each(function ($row) use ($varDate,$iteration) {

            $feed = DB::connection('mysql')
                    ->table('daily_feed')
                    ->updateOrInsert(
                        [
                            'datefeed' => $row->tanggal
                        ],
                        [
                            'customer' => $row->customer, 'staff' => $row->staff, 'trial'=> $row->trial,
                            'akademis'=> $row->academic, 'spm'=> $row->spm, 'jumlah'=> $row->jumlah
                        ]
                    );
            $this->count=$this->count+$iteration;

            $param = [
                'procName'=> '1',
                'finishValue' => 100,
                'procValue'=> round($this->count),
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
        });

        $lastid = DB::getPdo()->lastInsertId();
 
        // echo ''.$subQCustomer .',<br>\n Staff '.$subQStaff.
        // ',<br>\n Trial = '.$subQTrial.', 
        // <br>\n academic = '.$subQAcademic.', <br>\n spm = '.$subQSPM.',<br>\n '.$sSPM;
    }

    function porcessDua(Request $request){

        echo 'Running Process Monthly Transaction \n ';
        $varDate = $this->varDate($request->mydate);
        
        $tglSatu = Carbon::parse($request->mydate)->startOfMonth()->format('Ymd');
        $tglSPM = Carbon::parse($request->mydate)->format('Ymd');;
        echo ' first Day : '.$tglSatu.' now date '.$tglSPM.' mydate '.$request->mydate;
        $tglSatu = Carbon::parse($request->mydate)->startOfMonth();
        $tgl = Carbon::createFromFormat('Ymd',$request->mydate);
        $bln = $tgl->month;
        $thn = $tgl->year;
        echo ' tgl '.$tgl.',month '.$bln.', thn '.$thn;
        $finishValue = 100;

        $accAcademic = DB::connection('sqlsrv')
                        ->table('masteracc')
                        ->selectRaw('SUBSTRING(\'00000000\',1,8-LEN(no_cif))+ no_cif nocif')
                        ->whereIn('occupation',['9','6'])
                        ->whereNotIn('block',['C','D'])
                        ->pluck('nocif')
                        // ->implode(',')
                        // ->get()
                        ;
        
        $sSPM = DB::connection('sqlsrv')
                    ->table('subacc')
                    ->selectRaw('SUBSTRING(\'00000000\',1,8-LEN(no_cust))+ no_cust nocust')
                    ->whereIn('no_cust',function($query){
                        $query->select('no_cust')
                            ->from('sasol.dbo.spm');
                    })
                    ->whereRaw('datediff(mm,\''.$tglSPM.'\', dateadd(mm, 1, dateadd(yy,1,open_date))) > 0')
                    ->pluck('nocif')
                    // ->implode(',')
                    // ->get()
                    ;
        // echo ' subQCustomer '.$sSPM->count()>0?$accAcademic->push($sSPM)->implode(','):$accAcademic->implode(',');
        
        
        $param = [
            'procName'=> '2',
            'finishValue' => $finishValue,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);
        
        if ($accAcademic->count()>1000) {
            $rawQuery = '(ACCT_NO not in ('.($sSPM->count()>0?$accAcademic->slice(0,500)->push($sSPM)->implode(','):$accAcademic->slice(0,500)->implode(',')).')
                        OR ACCT_NO not in ('.($sSPM->count()>0?$accAcademic->slice(500,$accAcademic->count())->push($sSPM)->implode(','):$accAcademic->slice(500,$accAcademic->count())->implode(',')).')
                        )';
            $rawAcademic='(ACCT_NO IN ('.$accAcademic->slice(0,500)->implode(',').') 
                        OR ACCT_NO IN ('.$accAcademic->slice(500,$accAcademic->count())->implode(',').') 
                        )';
        } else {
            $rawQuery = 'ACCT_NO not in ('.($sSPM->count()>0?$accAcademic->push($sSPM)->implode(','):$accAcademic->implode(',')).')';
            $rawAcademic='(ACCT_NO IN ('.$accAcademic->slice(0,500)->implode(',').')';
        }
        
        $subQCustomer   = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('COUNT(DISTINCT(CUST_ID)) CNT')
                            ->join('MA001A02','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween('LOGIN_DATE',[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(USER_TP)'),'=','0')
                            ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            ->whereRaw($rawQuery)
                            // ->get()
                            ;
                            
        $subQStaff      = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('COUNT(DISTINCT(CUST_ID)) CNT')
                            ->whereBetween('LOGIN_DATE',[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(USER_TP)'),'=','1')
                            // ->get()
                            ;

        $subQTrial      = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw('COUNT(DISTINCT(CUST_ID)) CNT')
                            ->join('MA001A11','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween('LOGIN_DATE',[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            // ->get()
                            ;

        $subQAcademic   = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw(' COUNT(DISTINCT(CUST_ID)) CNT')
                            ->join('MA001A02','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween('LOGIN_DATE',[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            ->whereRaw($rawAcademic)
                            // ->get()
                            ;

        $subQSPM        = DB::connection('oracle')
                            ->table('CUST_CONN_LOG')
                            ->selectRaw(' COUNT(DISTINCT(CUST_ID)) CNT')
                            ->join('MA001A02','LOGIN_ID','=','CUST_ID','inner')
                            ->whereBetween('LOGIN_DATE',[$tglSatu,$tgl])
                            ->where(DB::raw('TRIM(MDIA_TP)'),'=','01')
                            ->whereRaw('ACCT_NO in ('.($sSPM->count()>0? $sSPM->implode(','):'\'\'').')' )
                            // ->get()
                            ;
        $param['procValue'] = 25;
        $this->loadingProgress($param);

        $monthFeed = DB::connection('oracle')->query()
                        ->fromSub($subQCustomer,'CUSTOMER')                        
                        ->crossJoinSub($subQStaff,'STAFF')
                        ->crossJoinSub($subQAcademic,'ACADEMIC')
                        ->crossJoinSub($subQSPM,'SPM')
                        ->crossJoinSub($subQTrial,'TRIAL')
                        ->selectRaw(' \'ACCUM\' as TOTAL,  
                                    CUSTOMER.CNT as CUSTOMER,   
                                    STAFF.CNT as STAFF,  
                                    TRIAL.CNT as TRIAL, 
                                    ACADEMIC.CNT as ACADEMIC, 
                                    SPM.CNT as SPM,
                                    CUSTOMER.CNT+STAFF.CNT+
                                    TRIAL.CNT+ACADEMIC.CNT+SPM.CNT as SUM')
                        ->get();
        
        $param['procValue'] = 30;
        $this->loadingProgress($param);

        $finishValue = $monthFeed->count();
        $iteration = 70/$finishValue;
        $this->count = 30;

        collect($monthFeed)->each(function ($row) use ($tgl, $varDate,$bln,$thn,$iteration){
            // print 'foreach '.$row->customer.'--\n';
            $feed = DB::connection('mysql')
                    ->table('monthly_feed')
                    ->updateOrInsert(
                        [
                            'bulan' => $bln, 'tahun' => $thn
                        ],
                        [
                            'datefeed' =>$tgl , 'customer' =>$row->customer , 'staff' =>$row->staff , 'trial' =>$row->trial , 'jumlah' =>$row->sum , 
                            'dateimport' =>$tgl, 'akademis' =>$row->academic, 'SPM' =>$row->spm 
                        ]
                    );
            
            $this->count=$this->count+$iteration;

            $param = [
                'procName'=> '2',
                'finishValue' => 100,
                'procValue'=> round($this->count),
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
        });
    }

    function processDailyTrans(Request $request){
        $varDate = $this->varDate($request->mydate);
        
        echo 'Running Process Daily Transaction \n ';

        $param = [
            'procName'=> '3',
            'finishValue' => 100,
            'procValue'=> 30,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $dailyTrans = DB::connection('oracle')
                    ->table('SE002T10')
                    ->selectRaw(' to_char(DT,\'YYYY-MM-DD\') "DATEID", nvl(CLOSEV_IDX,0) "CLOSEIDX", nvl(VAL,0) "VALUEIDX"')
                    ->where('DT','=',$varDate['tglmysql'])
                    ->where('Stock_code','=','COMPOSITE')
                    ->get()
                    ;
        echo ' -- '. $dailyTrans->count(). ' -- '. $varDate['tglmysql'];
        $finishValue = $dailyTrans->count(); 
        $iteration = 70/$finishValue;
        $this->count = 30;

        collect($dailyTrans)->each(function ($row) use ($varDate,$iteration){
            $trans = DB::connection('mysql')
                     ->table('daily_trans')
                     ->updateOrInsert(
                        [
                            'dateid'=>$varDate['tglmysql']
                        ],[ 
                            'closeidx'=>$row->closeidx, 'valueidx'=>$row->valueidx,
                            'bulan'=>$varDate['bln'], 'tahun'=>$varDate['thn'],
                            'dateimport'=>$varDate['tgl']
                        ]
                    );
                    
            $param = [
                'procName'=> '3',
                'finishValue' => 100,
                'procValue'=> $this->count=$this->count+round($iteration),
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
        });
    }

    function processBranchTrans(Request $request){
        $varDate = $this->varDate($request->mydate);

        echo 'Running Process Branch Transaction \n ';

        $param = [
            'procName'=> '4',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $subQBranch = DB::connection('oracle')
                        ->table('mt201t20','a')
                        ->selectRaw('nvl(bran_no,\'000\') Branch, sum(match_qty*match_price) match_amt, count(*) match_cnt')
                        ->join('mc303c10 b','b.stock_code','=','a.stock_code')
                        ->where('ord_date','=',$varDate['tglmysql'])
                        ->whereRaw('b.stock_type = \'O\'')
                        ->groupBy('bran_no');

        // $subQBranch   = DB::connection('oracle')
        //                 ->table('HLOGINLOG')
        //                 ->selectRaw('THE_DATE Branch,COUNT(DISTINCT(LOGIN_ID)) match_amt')
        //                 ->whereBetween('THE_DATE',[$varDate['tglsatu'],$varDate['tgl']])
        //                 ->where(DB::raw('TRIM(TYPE)'),'=','HTS')
        //                 ->groupBy('THE_DATE')
        //                 // ->get()
        //                 ;
        $subQComposite = DB::connection('oracle')
                        ->table('SE002T10')
                        ->selectRaw('nvl(VAL,0) tot_amt ')
                        ->where('dt','=',$varDate['tglmysql'])
                        ->where('stock_code','=','COMPOSITE');

        $subQCustomer = DB::connection('oracle')
                        ->table('ma001a03')
                        ->selectRaw('nvl(bran_no,\'000\') Branch, count(*) Customer_Cnt')
                        ->whereNotNull('s_id')
                        ->whereNull('close_date')
                        ->whereRaw('nvl(open_date,\'01-JAN-01\') <= TO_DATE(\''.$varDate['tglmysql'].'\',\'YYYY-MM-DD\')')
                        ->groupBy('bran_no')
                        ->orderBy('bran_no');

        $subQLogin = DB::connection('oracle')
                        ->table('hloginlog','a')
                        ->selectRaw('nvl(b.bran_no,\'000\') Branch, count(distinct a.login_id) login_cnt ')
                        ->join('MA001A03 b','a.acct_no','=','b.acct_no')
                        ->where('a.the_date','=',$varDate['tglmysql'])
                        ->where('type','=','HTS')
                        ->where('connect_flag','=','1')
                        ->groupBy('b.bran_no')
                        ->orderBy('b.bran_no');

        $subQMatch  =   DB::connection('oracle')
                        ->table('mt201t20','a')
                        ->selectRaw('sum(match_qty*match_price) match_amt ')
                        ->join('mc303c10 b','a.stock_code','=','b.stock_code')
                        ->where('b.stock_type','=','O')
                        ->where('a.ord_date','=',$varDate['tglmysql']);

        $subQNewCnt = DB::connection('oracle')
                        ->table('ma001a03')
                        ->selectRaw(' nvl(bran_no, \'000\') Branch, count(distinct bran_no) NEW_CNT ')
                        ->where('open_date','=',$varDate['tglmysql'])
                        ->whereNotNull('S_ID')
                        ->groupBy(DB::raw('rollup(bran_no)'))
                        ->orderBy('bran_no');

        $subQCloseCnt = DB::connection('oracle')
                        ->table('ma001a03')
                        ->selectRaw('nvl(bran_no, \'000\') Branch, count(distinct bran_no) CLOSE_CNT ')
                        ->where('close_date','=',$varDate['tglmysql'])
                        ->groupBy(DB::raw('rollup(bran_no)'))
                        ->orderBy('bran_no');


        $subQOnlineAmt = DB::connection('oracle')
                        ->table('mt201t20','a')
                        ->selectRaw('nvl(a.bran_no,\'000\') Branch, sum(a.match_qty*a.match_price) onlineamt')
                        ->join('mc303c10 b','b.stock_code','=','a.stock_code')
                        ->join('mt201t10 c','a.ord_date','=','c.ord_date')
                        ->whereRaw('b.stock_type=\'O\'')
                        ->whereRaw('c.channel=\'10\'')
                        ->whereRaw('a.ord_date =TO_DATE(\''.$varDate['tglmysql'].'\',\'YYYY-MM-DD\')'  )
                        ->whereRaw('a.bran_no = c.bran_no')
                        ->whereRaw('a.ord_no = c.ord_no')
                        ->groupBy('a.bran_no')
                        ->orderBy('a.bran_no')
                        ;

        $subQOfflineAmt = DB::connection('oracle')
                        ->table('mt201t20','a')
                        ->selectRaw(' nvl(a.bran_no,\'000\') Branch, sum(match_qty*match_price) offlineamt')
                        ->join('mc303c10 b','b.stock_code','=','a.stock_code')
                        ->join('mt201t10 c','a.ord_date','=','c.ord_date')
                        ->whereRaw('b.stock_type =\'O\'')
                        ->whereRaw('channel=\'00\'')
                        ->whereRaw('c.inpt_mdia_tp =\'00\' ')
                        ->whereRaw('a.ord_date =TO_DATE(\''.$varDate['tglmysql'].'\',\'YYYY-MM-DD\')'  )
                        ->whereRaw('a.bran_no = c.bran_no')
                        ->whereRaw('a.ord_no = c.ord_no')
                        ->groupBy('a.bran_no')
                        ->orderBy('a.bran_no');


        $feedDailyt =   DB::connection('oracle')->query()
                            ->fromSub($subQBranch,'BRCH')
                            ->selectRaw('BRCH.Branch "BRANCHID", BRCH.Match_Amt "MATCHAMT" , BRCH.Match_Cnt "MATCHCNT",
										round(BRCH.Match_Amt/(COMPOSITE.tot_amt*2)*100,15) "MS",
                                        round((BRCH.Match_Amt/MATCH.Match_amt * 100),2) "WEIGHT",CUST.Customer_Cnt "CUSTCNT" 
										,nvl(NEWCNT.NEW_CNT,0) "NEWCUST", nvl(CLOSECNT.CLOSE_CNT,0) "CLOSECUST"  ,  nvl(LOGIN.login_cnt,0) "LOGINCNT"
                                        , nvl(ONLNAMT.onlineamt,0) "ONLINEAMT" , nvl(OFFLNAMT.offlineamt,0) "OFFLINEAMT"
									  ')
                            ->crossJoinSub($subQComposite,'COMPOSITE') 
                            ->joinSub($subQCustomer,'CUST','CUST.branch','=','BRCH.branch','left outer')
                            ->joinSub($subQLogin,'LOGIN',DB::raw('substr(BRCH.Branch,1,3)'),'=','LOGIN.branch','left outer')
                            ->crossJoinSub($subQMatch,'MATCH')
                            ->joinSub($subQNewCnt,'NEWCNT','BRCH.branch','=','NEWCNT.branch','left outer')
                            ->joinSub($subQCloseCnt,'CLOSECNT','BRCH.branch','=','CLOSECNT.branch','left outer')         
                            ->joinSub($subQOfflineAmt,'OFFLNAMT','BRCH.branch','=','OFFLNAMT.branch','left outer')
                            ->joinSub( $subQOnlineAmt,'ONLNAMT','BRCH.branch','=','ONLNAMT.branch','left outer')
                            ->get()
                            ;
                            
        $param['procValue'] = 30;
        $this->loadingProgress($param);

        $finishValue = $feedDailyt->count();
        $iteration = 70/$finishValue;
        $this->count = 30;

        //|BRANCH|MATCHAMT|MATCHCNT|MS|WEIGHT|CUSTCNT|NEWCUST|CLOSECUST|LOGINCNT|OFFLINEAMT   |ONLINEAMT     |
        collect($feedDailyt)->each(function ($row) use ($varDate,$iteration){
            // echo ' -- '.$row->branchid.' -- '.$varDate['tglmysql'];
            $trans = DB::connection('mysql')
                     ->table('branch_trans')
                     ->updateOrInsert(
                        [
                            'dateid'=>$varDate['tglmysql'],
                            'branchid'=>$row->branchid
                        ],[ 
                            'matchamt'=>$row->matchamt,'matchcnt'=>$row->matchcnt,'ms'=>$row->ms,
                            'weight'=>$row->weight,'custcnt'=>$row->custcnt,'newcust'=>$row->newcust,
                            'closecust'=>$row->closecust,'logincnt'=>$row->logincnt,
                            'offlineamt'=>$row->offlineamt,'onlineamt'=>$row->onlineamt
                        ]
                    );
            
                    
            $this->count=$this->count+$iteration;

            $param = [
                'procName'=> '4',
                'finishValue' => 100,
                'procValue'=> round($this->count),
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
        });
    }

    function processMobileTrans(Request $request){
        $varDate = $this->varDate($request->mydate);
        
        echo 'Running Process Mobile Transaction \n ';

        $param = [
            'procName'=> '5',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);
        
        $subQMatch = DB::connection('oracle')
                    ->table('MT201T20','a')
                    ->selectRaw('nvl(bran_no, \'000\') Branch, 
                                sum(match_qty * match_price) match_amt, 
                                count(*) match_cnt ')
                    ->join('MC303C10 b','b.stock_code','=','a.stock_code','inner')
                    ->whereRaw('ord_date = TO_DATE(\''.$varDate['tglmysql'].'\',\'YYYY-MM-DD\') ')
                    ->where('b.stock_type','=','O')
                    ->where('inpt_mdia_tp','=','04')
                    ->groupByRaw('rollup(bran_no)')
                    ;

        $subQOrderCnt = DB::connection('oracle')
                        ->table('MT201T10')
                        ->selectRaw('nvl(bran_no, \'000\') Branch, 
                                    count(*) order_cnt  ')
                        ->whereRaw('ord_date = TO_DATE(\''.$varDate['tglmysql'].'\',\'YYYY-MM-DD\') ')
                        ->where('inpt_mdia_tp','=','04')
                        ->groupByRaw('rollup(bran_no)')
                        ;

        $subQBranch = DB::connection('oracle')
                        ->table('MT201T20','a')
                        ->selectRaw('nvl(bran_no, \'000\') Branch, 
                                    sum(match_qty * match_price) match_amt ')
                        ->join('MC303C10 b','b.stock_code','=','a.stock_code','inner')
                        ->whereRaw('ord_date = TO_DATE(\''.$varDate['tglmysql'].'\',\'YYYY-MM-DD\') ')
                        ->whereRaw('b.stock_type = \'O\'')
                        ->groupByRaw('rollup(bran_no)')
                        ;

        $mobilfeed = DB::connection('oracle')
                        ->query()
                        ->fromSub($subQBranch, 'brch')
                        ->selectRaw(' brch.branch, 
                        mcth.match_amt, 
                        mcth.match_cnt, 
                        cnt.order_cnt, 
                        to_char(
                          round(
                            (mcth.match_amt / brch.match_amt)* 100, 
                            4
                          ), 
                          \'990.9999\'
                        ) "Weight" ')
                        ->joinSub($subQMatch,'mcth','brch.branch','=','MCTH.branch','left outer ')
                        ->joinSub($subQOrderCnt,'cnt','cnt.branch','=','brch.branch','inner')
                        // ->get()
                        ;

        // echo 'mobile feed '.$mobilfeed->toSql();
        
        $param['procValue'] = 30;
        $this->loadingProgress($param);

        $finishValue = $mobilfeed->count();
        $iteration = 70/$finishValue;
        $this->count = 30;
        
        collect($mobilfeed->get())->each(function($row) use ($varDate,$iteration)
            {
                $trans = DB::connection('mysql')
                            ->table('mobile_trans')
                            ->updateOrInsert(
                                [
                                    'dateid'=>$varDate['tglmysql'],
                                    'branch'=>$row->branch
                                ],
                                [
                                    'match_amt'=>$row->match_amt,
                                    'match_cnt'=>$row->match_cnt,
                                    'order_cnt'=>$row->order_cnt,
                                    'Weight'=>$row->weight
                                ]
                                );
                                
                $param = [
                    'procName'=> '5',
                    'finishValue' => 100,
                    'procValue'=> $this->count=$this->count+round($iteration),
                    'date' => $varDate['tglmysql']
                ];
                $this->loadingProgress($param);
            }
        );
    }

    function processClientTrans(Request $request){
        $varDate = $this->varDate($request->mydate);
        
        echo 'Running Process Client Transaction \n ';

        $param = [
            'procName'=> '6',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $clientTrans = DB::connection('oracle')
                        ->table('MT201T20', 'mtch')
                        ->selectRaw('mtch.ACCT_NO, CLIENT_NAME, sum(MATCH_QTY*MATCH_PRICE) as MATCH_AMT')
                        ->join('MA001A03 cust','cust.acct_no','=','mtch.acct_no','inner')
                        ->where('ord_date','=',$varDate['tglmysql'])
                        ->groupByRaw('mtch.acct_no,cust.client_name')
                        ->get();
        
        $param['procValue'] = 30;
        $this->loadingProgress($param);
        
        $finishValue = $clientTrans->count();
        $iteration = 70/$finishValue;
        $this->count = 30;

        collect($clientTrans)->each(function($row) use ($varDate,$iteration)
            {
                $upsert = DB::connection('mysql')
                      ->table('client_trans')
                      ->updateOrInsert(
                        [
                            'dateid'=>$varDate['tglmysql'],'acct_no'=>$row->acct_no
                        ],[ 
                            'client_name'=>$row->client_name ,
                            'match_amt'=>$row-> match_amt
                        ]
                        );
                
                $this->count=$this->count+$iteration;

                $param = [
                    'procName'=> '6',
                    'finishValue' => 100,
                    'procValue'=> round($this->count),
                    'date' => $varDate['tglmysql']
                ];
                $this->loadingProgress($param);

            }
        );

                    
    }

    function processDetailTrans(Request $request){
        $varDate = $this->varDate($request->mydate);
        $finishValue = 100;
        
        echo 'Running Process Detail Transaction \n ';

        $param = [
            'procName'=> '7',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $detailTrans = DB::connection('oracle')
                        ->table('MT201T20', 'trans')
                        ->selectRaw('trans.ACCT_NO,
                                    cust.CLIENT_NAME,
                                    trans.STOCK_CODE,
                                    DECODE(SIDE, \'1\', \'BUY\', \'2\', \'SELL\') AS SIDE,
                                    MATCH_PRICE,
                                    SUM(MATCH_QTY)                        AS MATCH_QTY,
                                    SUM(MATCH_PRICE * MATCH_QTY)          AS MATCH_AMT
                        ')
                        ->join('MA001A03 cust','cust.acct_no','=','trans.acct_no')
                        ->where('ord_date','=',$varDate['tglmysql'])
                        ->groupByRaw('
                                    trans.ACCT_NO,
                                    cust.CLIENT_NAME,
                                    trans.STOCK_CODE,
                                    SIDE,
                                    MATCH_PRICE')
                        ->orderBy('trans.stock_code','asc')
                        ->orderBy('match_amt','desc')
                        ;
        // echo ' -- '.$detailTrans->toSql();
        
        $param['procValue'] = 25;
        $this->loadingProgress($param);
        
        $finishValue = $detailTrans->get()->count();
        $iteration = 70/$finishValue;
        $this->count = 30;

        // $delete = DB::connection('mysql')
        //         ->delete('delete from detail_trans where dateid = ?',[$varDate['tglmysql']]);
        $delete = DB::connection('mysql')
                    ->table('detail_trans')
                    ->where('dateid',[$varDate['tglmysql']])
                    ->delete();
                    

        $param['procValue'] = 30;
        $this->loadingProgress($param);

        collect($detailTrans->get())->each(function($row) use ($varDate,$iteration){
            $save = DB::connection('mysql')
                    ->table('detail_trans')
                    ->Insert(
                        [
                            'dateid'=>$varDate['tglmysql'],
                            'acct_no'=>$row->acct_no,'stock_code'=>$row->stock_code,
                            'side'=>$row->side,'match_price'=>$row->match_price,
                            'match_qty'=>$row->match_qty,'amount'=>$row->match_amt,
                        ]
                    );
            $this->count=$this->count+$iteration;

            $param = [
                'procName'=> '7',
                'finishValue' => 100,
                'procValue'=> round($this->count),
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
        });



    }

    function processNegoTrans(Request $request){
        $varDate = $this->varDate($request->mydate);
        
        echo 'Running Process Nego Transaction \n ';

        $param = [
            'procName'=> '8',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $subQOrdNg = DB::connection('oracle')
                        ->table('SE002T00')
                        ->selectRaw('SUBSTR(TO_CHAR(DT, \'YYYYMMDD\'), 1, 10) "DATEID",
                                    VOL*LOT_SIZE*100 "VOL",
                                    VAL,
                                    BUY_FVAL,
                                    SELL_FVAL')
                        ->whereRaw(' DT >= TO_DATE(\''.$varDate['tglmysql'].'\', \'YYYY-MM-DD\')')
                        ->whereRaw(' DT <= TO_DATE(\''.$varDate['tglmysql'].'\', \'YYYY-MM-DD\')')
                        ->where('board_code','=','NG')
                        ->where('stock_type','=','O')
                        ->where('vol','>',0)
                        ;

        $feedOrdNG = DB::connection('oracle')
                    ->query()
                    ->fromSub($subQOrdNg,'ordng')
                    ->selectRaw('
                                DATEID,
                                ROUND(SUM(VOL)/1000, 0)          VOL_THOU,
                                ROUND(SUM(VAL)/1000000, 0)       VAL_MILL,
                                ROUND(SUM(BUY_FVAL)/1000000, 0)  FBVAL_MILL,
                                ROUND(SUM(SELL_FVAL)/1000000, 0) FSVAL_MILL')
                    ->groupBy('dateid')
                    ->orderBy('dateid')
                    ;
        // echo 'orderNG daily '.$feedOrdNG->get();
        
        $param['procValue'] = 30;
        $this->loadingProgress($param);
        
        $finishValue = $feedOrdNG->get()->count();
        $iteration = 40/$finishValue;
        $this->count = 30;
        
        collect($feedOrdNG->get())->each(function($row) use ($varDate,$iteration)
            {
                $query = DB::connection('mysql')->table('ng_daily')
                        ->updateOrInsert(
                            [
                                'dateid'=>$varDate['tglmysql'],
                            ],
                            [
                                'vol_thou'=>$row->vol_thou,
                                'val_mill'=>$row->val_mill,
                                'fbval_mill'=>$row->fbval_mill,
                                'fsval_mill'=>$row->fsval_mill
                            ]
                            );
                            
            $param = [
                'procName'=> '8',
                'finishValue' => 70,
                'procValue'=> $this->count=$this->count+$iteration,
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
            }
        );

        $subQOrdNgMontly = DB::connection('oracle')
                        ->table('SE002T00')
                        ->selectRaw('substr(to_char(DT,\'YYYY-MM-DD\'),1,7) "DATEID",
                                    VOL*100 "VOL", 
                                    VAL, 
                                    buy_fval, 
                                    sell_fval ')
                        ->whereRaw(' DT >= TO_DATE(\''.$varDate['tgl']->copy()->startOfYear()->format('Y-m-d').'\', \'YYYY-MM-DD\')')
                        ->whereRaw(' DT <= TO_DATE(\''.$varDate['tglmysql'].'\', \'YYYY-MM-DD\')')
                        ->where('board_code','=','NG')
                        ->where('stock_type','=','O')
                        ->where('vol','>',0)
                        ;
                        
                        
        $feedOrdNGMonhtly = DB::connection('oracle')
                    ->query()
                    ->fromSub($subQOrdNgMontly,'ordng')
                    ->selectRaw('
                                DATEID,
                                round(sum(VOL)/1000000,0) VOL_Mill,  
                                round(sum(VAL)/1000000000,0) VAL_Bill,  
                                round(sum(buy_fval)/1000000000,0) FBUY_Bill,  
                                round(sum(sell_fval)/1000000000,0) FSELL_Bill' )
                    ->groupBy('dateid')
                    ->orderBy('dateid')
                    ;

        $finishValue = $feedOrdNGMonhtly->get()->count();
        $iteration = 30/$finishValue;
        $this->count = 70;
        
        collect($feedOrdNGMonhtly->get())->each(function($row) use ($varDate,$iteration)
            {
                $query = DB::connection('mysql')->table('ng_monthly')
                        ->updateOrInsert(
                            [
                                'dateid'=>$row->dateid.'-01',
                            ],
                            [
                                'VOL_Mill'=>$row->vol_mill,
                                'VAL_Bill'=>$row->val_bill,
                                'FBUY_Bill'=>$row->fbuy_bill,
                                'FSELL_Bill'=>$row->fsell_bill
                            ]
                            );
                            
            $param = [
                'procName'=> '8',
                'finishValue' => 100,
                'procValue'=> $this->count=$this->count+$iteration,
                'date' => $varDate['tglmysql']
            ];
            $this->loadingProgress($param);
            }
        );   

    }

    function processKomisiInterest(Request $request){
        $varDate = $this->varDate($request->mydate);
        $varDate['tgl']->subDay(1)->format('Y-m-d');
        echo 'Running Process Komisi Interest \n ';
        $param = [
            'procName'=> '9',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);
        // cek last insert date 
        $bl = true;
        while ($bl) { 
            # code...
            // if backdate not holliday 
            $isHoliday = 
            $bl = false;
        }
        $komisi = DB::connection('sqlsrv')
                  ->table('invoice')
                  ->selectRaw('no_cust, name, sum(comm) as komisi ')
                  ->join('masteracc','no_cif','=','no_cust','inner')
                  ->where('dt_inv','=',$varDate['tgl']->format('Y-m-d'))
                  ->groupByRaw('name,no_cust')
                  ;
                  
        collect($komisi->get())->each(function($row) use ($varDate){

            $upsert = DB::connection('mysql')
                      ->table('client_grade')
                      ->updateOrInsert(
                        [
                            'date_id'=>$varDate['tgl']->format('Y-m-d'),'acct_no'=>$row->no_cust
                        ],[
                            'acct_name'=>$row->name,
                            'commission'=>$row->komisi,
                            'interest'=>0,
                            'stock_value'=>0
                        ]
                        );
        });
        
        $interest = DB::connection('sqlsrv')
                    ->table('journal')
                    ->selectRaw('no_soa, name, dbamt')
                    ->join('masteracc','no_cif','=','no_soa','inner')
                    ->where('jrn_dt','=',$varDate['tgl']->format('Y-m-d'))
                    ->where('divisi','=','Z')
                    ->where('no_soa','<>','01')
                    ->whereRaw('isnumeric(no_soa)>0')
                    ->whereRaw('(no_coa=\'1203\' or no_coa=\'1202\')')
                    ->where('dbamt','>',0);
       
        $param['procValue'] = 30;
        $this->loadingProgress($param);
        
        $finishValue = $interest->get()->count();
        if ($finishValue==0) {            
            $param = [
                'procName'=> '9',
                'finishValue' => 100,
                'procValue'=> 100
            ];
            $this->loadingProgress($param);
        }
        else {
            echo 'tgl backdate '. $varDate['tgl']->format('Y-m-d');
            $iteration = 70/$finishValue;
            $this->count = 30;

            collect($interest->get())->each(function($row) use ($varDate,$iteration){
                $client_grade = DB::connection('mysql')
                                ->table('client_grade')
                                ->where('date_id','=',$varDate['tgl']->format('Y-m-d'))
                                ->where('acct_no','=',$row->no_soa)
                                ->first()
                                ;
                if ($client_grade != null) {
                    $update = DB::connection('mysql')
                            ->table('client_grade')
                            ->where('date_id','=',$varDate['tgl']->format('Y-m-d'))
                            ->where('acct_no','=',$row->no_soa)
                            ->update(['acct_name'=>$row->name,
                            'interest'=>$row->dbamt]);
                } else {
                    $insert = DB::connection('mysql')
                            ->table('client_grade')
                            ->insert([
                                'date_id'=>$varDate['tgl']->format('Y-m-d'),
                                'acct_no'=>$row->no_soa,                            
                                'acct_name'=>$row->name,
                                'commission'=>0,
                                'interest'=>$row->dbamt,
                                'stock_value'=>0
                            ])        
                    ;
                }
                $this->count=$this->count+$iteration;

                $param = [
                    'procName'=> '9',
                    'finishValue' => 100,
                    'procValue'=> round($this->count),
                    'date' => $varDate['tglmysql']
                ];
                $this->loadingProgress($param);
            });
        }  

    }

    function processClientName(Request $request){
        $varDate = $this->varDate($request->mydate);
        
        echo 'Running Process Client Name\n ';

        $param = [
            'procName'=> '10',
            'finishValue' => 100,
            'procValue'=> 5,
            'date' => $varDate['tglmysql']
        ];
        $this->loadingProgress($param);

        $cleanClient = DB::connection('mysql')
                    ->table('client_name')
                    ->delete();
        echo $cleanClient.' status clean client';
        
        $param['procValue'] = 30;
        $this->loadingProgress($param);
        

        if ($cleanClient > 0) {
            $getClientHero = DB::connection('oracle')
                             ->table('ma001a03')
                            //  ->select(['acct_no','client_name'])
                             ->selectRaw('acct_no, client_name acct_name')
                             ->where('acct_no','>','00000000')
                             ->get() 
                             ->toArray();
            $to_fill = [];
            
            $finishValue = count($getClientHero);
            $iteration = 70/$finishValue;
            $this->count = 30;

            foreach ($getClientHero as $record) {
                $to_fill[] = (array)$record;
                $this->count=$this->count+$iteration;
                $param = [
                    'procName'=> '10',
                    'finishValue' => 100,
                    'procValue'=> round($this->count),
                    'date' => $varDate['tglmysql']
                ];
                $this->loadingProgress($param);
            }
            $insert = DB::connection('mysql')
                      ->table('client_name')
                      ->insert($to_fill);
            
        }
    }

    function loadingProgress($param=[]){
        /**
         * param value 
         * process name , finishValue , processValue
         */
        $procName = $param['procName'];
        $finishValue = $param['finishValue'];
        $procValue  = $param['procValue'];
        //publish to pub 
        $percent = $procValue * ($finishValue/100);
        // array_push($param,$percent);
        // Redis::publish('create:blog',json_encode($param));
        Event(new \App\Events\SendMessage($param));
        Log::info(' '.$procName.' '.$percent);
    }

    function startProgress(){
        $param = [
            'procName'=> 'startProgress'
        ];
        //simpan ke feed_process_detail 
        Event(new \App\Events\SendMessage($param));
    }

    function endProgress($tgl){
        //simpan ke feed_process
        $status = DB::connection('mysql')
                    ->table("feed_process")
                    ->insert(['process_date'=>  $tgl['tglmysql'],
                    'status'=> '1']);

        $param = ['procName'=> 'endProgress'];
        Event(new \App\Events\SendMessage($param));
    }

    function detailProgress($procName,$tgl){

        $status = DB::connection('mysql')
        ->table("feed_process_detail")
        ->insert([
            'process_date' => $tgl['tglmysql'],
            'process_name' => $procName,
            'status' => 100
        ]);
    }

    function getProgress($dateFeed){
        $date = $this->varDate($dateFeed);
        // cek holiday 
        $isHoliday = DB::connection('oracle')
                        ->table('MC302C12')
                        ->where('THE_DATE','=',$date['tglmysql'])
                        ->first('HOLI_DESC');

        // get feed detail
        $feed_detail = DB::connection('mysql')
                        ->table('feed_process_detail')
                        ->where('process_date','=',$date['tglmysql'])
                        ->get();
        // get feed status 
        $feed = db::connection('mysql')
                ->table('feed_process')
                ->where('process_date','=',$date['tglmysql'])
                ->get();
        
        return array('isHoliday'=> $isHoliday,
            'feed_detail' => $feed_detail,
            'feed_process' => $feed, 
            'subday'=>$date['tgl']->subDay(1)
        );
    }
}