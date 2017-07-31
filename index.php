<?php
include_once('sql.php');

$mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
    'Maggio', 'Giugno', 'Luglio', 'Agosto',
    'Settembre', 'Ottobre', 'Novembre','Dicembre');

$giorni = array('Domenica','Lunedì','Martedì','Mercoledì',
    'Giovedì','Venerdì','Sabato');
$anno=date('Y');
if(isset($_GET['mese']))
    $mese=$_GET['mese'];
else
    $mese=date('m');
if(isset($_GET['giorno']))
    $giorno=$_GET['giorno'];
else{
    $res = query("SELECT DAY(data) as giorno FROM `activity` WHERE MONTH(data) = '$mese' AND YEAR(data) = '$anno' ORDER BY data desc LIMIT 1", $conn, true);
    $giorno = $res["giorno"];
}
$res = query("SELECT DISTINCT(MONTH(date(data))) as data FROM `activity` WHERE YEAR(data) = '$anno' GROUP BY data ORDER BY data", $conn);
$out3 = '';
while (($ra = mysqli_fetch_assoc($res))) {
    #$phpdate = strtotime( $ra["data"] );
    $mmx = $ra["data"];
    $mesix=$mesi[intval($mmx)];
    $sce='';
    $act='';
    if ($mese == $mmx) {
        $sce = '<span class="sr-only">(current)</span>';
        $act='active';
    }
    $out3.='<li class="nav-item '.$act.'"><a class="nav-link" href="index.php?mese='.$mmx.'">'.$mesix.' '.$sce.'</a></li>';
}
$res = query("SELECT DISTINCT(date(data)) as data FROM `activity` WHERE MONTH(data) = '$mese' AND YEAR(data) = '$anno' GROUP BY data ORDER BY data desc", $conn);
$out2 = '';
while (($ra = mysqli_fetch_assoc($res))) {
    $phpdate = strtotime( $ra["data"] );
    $day2 = date('l', $phpdate);
    $day = date('d',  $phpdate);
    $mm = date('m',   $phpdate);
    $mm2 = date('F',  $phpdate);
    $ww = date('w',  $phpdate);
    $sce='';
    $act='';
    if ($giorno == $day && $mese == $mm) {
        $sce = '<span class="sr-only">(current)</span>';
        $act='active';
    }
    $out2.='<li class="nav-item"><a class="nav-link '.$act.'" href="index.php?mese='.$mm.'&giorno='.$day.'">'.$giorni[$ww].' '.$day.' '.$sce.'</a></li>';
}
$ricerca = $anno . '-' . $mese . '-' . $giorno;
$res = query("SELECT * FROM `activity` where DATE(data) = '$ricerca' ORDER BY ora_fine", $conn);
$mini=0;
$out = '';
while (($ra = mysqli_fetch_assoc($res))) {
    $phpdate = strtotime( $ra["data"] );
    $mysqldate = date( 'd/m/Y H:i', $phpdate );
    $phpdate = strtotime( $ra["ora_fine"] );
    $mysqldate_f = date( 'H:i', $phpdate );
    $clpau='';
    if ($ra["tipo"] != 'pausa')
        $mini+=$ra["minuti"];
    else{
        $clpau='forpau';
        $ra["cliente"]='';
    }
    $min=gmdate("H:i", $ra["minuti"]*60);
    $sp = explode(" ", $mysqldate);
    $data_corr=$sp[0];
    $out .= '<tr class="'.$clpau.'">
<td>' . $ra["proj"] . '</td>
<td>' . $ra["cliente"] . '</td>
<td>' . ucfirst($ra["descr"]) . '</td>
<td align="center">' . $sp[1] . '</td>
<td align="center">' .  $mysqldate_f . '</td>
<td align="center">' . $min . '</td>
</tr>';
}
$min2=gmdate("H:i", $mini*60);
$mini2=0;
$res = query("SELECT data, verso, minuti, ora_fine, act_phone.id_cliente, nome FROM `act_phone` left join act_cliente on act_phone.id_cliente = act_cliente.id_cliente WHERE DATE(data) = '$ricerca' ORDER BY minuti desc", $conn);
$out4 = '';
while (($ra = mysqli_fetch_assoc($res))) {
    $phpdate = strtotime( $ra["data"] );
    $mysqldate = date( 'd/m/Y H:i', $phpdate );
    $phpdate = strtotime( $ra["ora_fine"] );
    $mysqldate_f = date( 'H:i', $phpdate );
    $clpau='';
    $mmm=explode(":",$ra["minuti"]);
    $mini2+=(intval($mmm[0])*3600+intval($mmm[1])*60+intval($mmm[2]));
    //$mini2=120;
    if ($ra["verso"] == 1){
        $verso='<img src="img/go-button2.png">';
    }
    else{
        $verso='<img src="img/go-button1.png">';
        //$clpau='forpau';
        //$ra["cliente"]='';
    }
    $min=$mmm[0].':'.$mmm[1];
    $sp = explode(" ", $mysqldate);
    $out4 .= '<tr class="'.$clpau.'">
<td align="center">' . $verso . '</td>
<td>' . $ra["nome"] . '</td>
<td align="center">' . $ra["id_cliente"] . '</td>
<td align="center">' . $sp[1] . '</td>
<td align="center">' .  $mysqldate_f . '</td>
<td align="center"><abbr title="'.$ra["minuti"].'">' . $min . '</abbr></td>
</tr>';
}
$min3=gmdate("H:i", $mini2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/img/favicon.ico">
    <title>Dashboard Template for Bootstrap</title>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
    <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse"
            data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <img src="img/ico.png" alt="Logo Asten" width="34" style="margin-right: 10px"><a class="navbar-brand" href="#">Mesi:</a>
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <?=$out3?>
        </ul>
        <form class="form-inline mt-2 mt-md-0">
            <!--            <input class="form-control mr-sm-2" type="text" placeholder="Search">-->
            <!--            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>-->
        </form>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
            <ul class="nav nav-pills flex-column">
              <?=$out2?>
            </ul>
        </nav>
        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9">
            <h2>Attività di <?=$mesi[intval($mm)]?>: <span class="blu">Marco Dal Borgo</span></h2>
            <h4>Data: <span class="blu"><?=$data_corr?></span></h4>
                    </div>
                    <div class="col-md-3" style="text-align: right"><img src="img/sc.png" width="80" class="img-fluid rounded-circle logo pull-right" alt="Sceriffo" ></div>
                </div>
            </div>
            <!-- <section class="row text-center placeholders">
                 <div class="col-6 col-sm-3 placeholder">
                     <img src="data:image/gif;base64,R0lGODlhAQABAIABAAJ12AAAACwAAAAAAQABAAACAkQBADs=" width="200" height="200" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail">
                     <h4>Label</h4>
                     <div class="text-muted">Something else</div>ufficio_fotocop
                 </div>
                 <div class="col-6 col-sm-3 placeholder">
                     <img src="data:image/gif;base64,R0lGODlhAQABAIABAADcgwAAACwAAAAAAQABAAACAkQBADs=" width="200" height="200" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail">
                     <h4>Label</h4>
                     <span class="text-muted">Something else</span>
                 </div>
                 <div class="col-6 col-sm-3 placeholder">
                     <img src="data:image/gif;base64,R0lGODlhAQABAIABAAJ12AAAACwAAAAAAQABAAACAkQBADs=" width="200" height="200" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail"><h4>Telefonate Gestite</h4>
                     <h4>Label</h4>
                     <span class="text-muted">Something else</span>
                 </div>
                 <div class="col-6 col-sm-3 placeholder">
                     <img src="data:image/gif;base64,R0lGODlhAQABAIABAADcgwAAACwAAAAAAQABAAACAkQBADs=" width="200" height="200" class="img-fluid rounded-circle" alt="Generic placeholder thumbnail">
                     <h4>Label</h4>
                     <span class="text-muted">Something else</span>
                 </div>
             </section>-->

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Progetto</th>
                        <th>Cliente</th>
                        <th>Descrizione</th>
                        <th style="text-align: center">Ora Inizio</th>
                        <th style="text-align: center">Ora Fine</th>
                        <th style="text-align: center">Tempo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?=$out?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" align="right"><strong>Totale lavorato:</strong></td>
                        <td colspan="1" align="center"><?=$min2?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12"><h4>Telefonate Gestite</h4></div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: center">In/Out</th>
                        <th>Cliente</th>
                        <th style="text-align: center">Numero</th>
                        <th style="text-align: center">Ora Inizio</th>
                        <th style="text-align: center">Ora Fine</th>
                        <th style="text-align: center">Tempo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?=$out4?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" align="right"><strong>Totale:</strong></td>
                        <td colspan="1" align="center"><?=$min3?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </main>
    </div>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"
        integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n"
        crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
        crossorigin="anonymous"></script>
<script src="dist/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="dist/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>