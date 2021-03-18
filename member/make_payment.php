<?php 

if(!isset($_GET['userId']) && !isset($_GET['monthId']) && !isset($_GET['amount'])){
    header("Location:index.php");
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Make Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" type="text/css" href="../vendor/payment_assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../vendor/payment_assets/font-awesome/css/font-awesome.min.css" />

    <script type="text/javascript" src="../vendor/payment_assets/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="../vendor/payment_assets/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">

<div class="page-header">
    <h1>Total Payment of $<?php echo number_format(floatval( $_GET['amount']), 2, '.', '');?></small></h1>
</div>

<!-- Credit Card Payment Form - START -->

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <h3 class="text-center">Payment Details</h3>
                        <img class="img-responsive cc-img" src="http://www.prepbootstrap.com/Content/images/shared/misc/creditcardicons.png">
                    </div>
                </div>
                <div class="panel-body">
                    <form role="form">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CARD NUMBER</label>
                                    <div class="input-group">
                                        <input type="tel" class="form-control" placeholder="Valid Card Number" />
                                        <span class="input-group-addon"><span class="fa fa-credit-card"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7 col-md-7">
                                <div class="form-group">
                                    <label><span class="hidden-xs">EXPIRATION</span><span class="visible-xs-inline">EXP</span> DATE</label>
                                    <input type="tel" class="form-control" placeholder="MM / YY" />
                                </div>
                            </div>
                            <div class="col-xs-5 col-md-5 pull-right">
                                <div class="form-group">
                                    <label>CV CODE</label>
                                    <input type="tel" class="form-control" placeholder="CVC" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>CARD OWNER</label>
                                    <input type="text" class="form-control" placeholder="Card Owner Names" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="make-payment" class="btn btn-warning btn-lg btn-block">Process payment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cc-img {
        margin: 0 auto;
    }
</style>

</div>

<script src="../config/api.js"></script>

<script>
    document.getElementById('make-payment').addEventListener('click', async () => {
        var userId = "<?php echo $_GET['userId']; ?>";
        var monthId = "<?php echo $_GET['monthId']; ?>";

        try{
            var result = await fetch(getURL("payments/make_payments.php?id=" + userId + "&monthId=" + monthId));
            result = await result.json();
            
            if(result.succ){
                window.location.replace("payments.php");
            }
        }catch(error){
            console.error(error);
        }
    });
</script>

</body>
</html>