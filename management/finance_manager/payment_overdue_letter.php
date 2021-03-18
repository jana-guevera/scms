<?php 

require_once "../../config/database.php";
require_once "../../models/member.php";

$database = Database::getInstance();
$member = new Member($database->getConnection());

$member->id = $_GET['id'];
$fetchedMember = $member->readOne()->fetchAll()[0];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
</head>
<body>
    <div class="container p-5">
        <h4 class="mb-4">Payment Overdue Letter</h4>
        <h6 class="mb-4"><?php echo $fetchedMember['name'] ?></h6>
        <h6 class="mb-4"><?php echo $fetchedMember['address'] ?></h6>
        <h6 class="mb-4"><?php echo date("d/m/Y")?></h6>

        <p>Dear Sirs,</p>
        <p>Our Ref: <?php echo $fetchedMember['name'] ?></p>
        <p>It has come to our attention that your account is overdue for payment. </p>
        <p>We are not aware of any disputes or reason for non-payment, 
          therefore we would respectfully remind you that you have exceeded the trading terms for these outstanding 
          amounts and we would be grateful to receive your remittance as soon as possible.
        </p>
        <p>The details of the outstanding invoices are as follows: </p>

        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Payment ID</th>
              <th>Payment For</th>
              <th>Due Date</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr>
              <td>1</td>
              <td>Monthly Payment</td>
              <td>12/12/2021</td>
              <td>$2020</td>
            </tr>
          </tbody>
        </table>

        <p>We look forward to hearing from you.</p>
        <p>Yours sincerely</p>
        <p>On behalf of Sussex Companions</p>
    </div>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="../../config/api.js"></script>

    <script>
      const fetchData = async () => {
        const memberId = "<?php echo $_GET['id']?>";
        const monthId = "<?php echo $_GET['monthId']?>";

        var result = await fetch(getURL("payments/monthly_payments.php?id=" + memberId + "&monthId=" + monthId));
        result = await result.json();

        $('#tbody').empty();

        result.records.forEach(pay => {
          if(pay.status == 2){
            var tr = `
              <tr>
                <td>${pay.id}</td>
                <td>${pay.paymentFor}</td>
                <td>12/12/2021</td>
                <td>$${pay.amount}</td>
              </tr>
            `;

            $('#tbody').append(tr);
          }
        });

        window.print();
      }


      fetchData();
    </script>
</body>
</html>