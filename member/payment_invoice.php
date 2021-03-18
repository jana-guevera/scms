<?php
  
require_once "../config/database.php";
require_once "../models/member.php";

$database = Database::getInstance();
$member = new Member($database->getConnection());

$member->id = $_GET['id'];
$fetchedMember = $member->readOne()->fetchAll()[0];

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="../vendor/invoice/style.css" media="all" />
  </head>
  <body style="padding: 10px;">
    <header class="clearfix">
      <div id="logo">
        <img src="../vendor/invoice/logo.png">
      </div>
      <div id="company">
        <h2 class="name">Sussex Companions</h2>
        <div>283/54 Grandpass Road, Colombo-14</div>
        <div>(077) 586-0597</div>
        <div><a href="#">contact@sussex.com</a></div>
      </div>
      </div>
    </header>
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">INVOICE TO:</div>
          <h2 class="name"><?php echo $fetchedMember['name']; ?></h2>
        <?php if($fetchedMember['address'] != ""){?>
                    <div class="address"><?php echo $fetchedMember['address'];?></div> 
        <?php   }?>
          <div class="email"><a href="#"><?php echo $fetchedMember['email'];?></a></div>
        </div>
        <div id="invoice">
          <h1 id="bill-month">Bill Month 12/12/2020</h1>
          <div class="date">Date of Bill: <?php echo date("d/m/Y")?></div>
        </div>
      </div>
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">#</th>
            <th class="desc">Payment For</th>
            <th class="unit">Payment Status</th>
            <th class="total">Amount</th>
          </tr>
        </thead>
        <tbody id="tbody">
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"></td>
            <td colspan="1">SUBTOTAL</td>
            <td id="subtotal">Rs 2020</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="1">Paid Amount</td>
            <td id="paid-amount">Rs 00.00</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="1">Balance</td>
            <td id="balance">Rs 202</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="1">GRAND TOTAL</td>
            <td id="grand-total">Rs 2020</td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
   
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="../config/api.js"></script>

    <script>
      const fetchData = async () => {
        const memberId = "<?php echo $_GET['id']?>";
        const monthId = "<?php echo $_GET['monthId']?>";

        var total = 0;
        var paidAmount = 0;

        var result = await fetch(getURL("payments/monthly_payments.php?id=" + memberId + "&monthId=" + monthId));
        result = await result.json();

        result.records.forEach(pay => {
          var paymentStatus;
          total += parseInt(pay.amount);

          if(pay.status == 0){
              paymentStatus = 'Unpaid';
          }else if(pay.status == 1){
            paidAmount += parseInt(pay.amount);
            paymentStatus = 'Paid';
          }else{
            paymentStatus = 'Overdue';
          }

          var tr = `
              <tr>
                <td class='no' style="text-align:center">${pay.id}</td>
                <td class='desc'><h3>${pay.paymentFor}</h3> ${pay.name}</td>
                <td class="unit" style="text-align:center">${paymentStatus}</td>
                <td>$${pay.amount}</td>
              </tr>
            `;

            $('#tbody').append(tr);
        });

        document.getElementById("subtotal").innerHTML = "$" + total;
        document.getElementById("paid-amount").innerHTML = "$" + paidAmount;
        document.getElementById("balance").innerHTML = "$" + (total - paidAmount);
        document.getElementById("grand-total").innerHTML = "$" + total;
        document.getElementById("bill-month").innerHTML = "Bill Month " + result.records[0].month;

        window.print();
      }


      fetchData();
    </script>
  </body>
</html>