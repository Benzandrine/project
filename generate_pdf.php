<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $receiving_id = $_GET['id'];
    $receiving = $conn->query("SELECT * FROM receiving_list WHERE id=" . $receiving_id)->fetch_assoc();
    $supplier_id = $receiving['supplier_id'];
    $supplier = $conn->query("SELECT supplier_name FROM supplier_list WHERE id=" . $supplier_id)->fetch_assoc();
    $inv = $conn->query("SELECT * FROM inventory WHERE type=1 AND form_id=" . $receiving_id);
}

$total_qty = 0;
$total_price = 0;
?>

<div class="container-fluid" id="print-receiving">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .wborder {
            border: 1px solid gray;
        }
        .bbottom {
            border-bottom: 1px solid black;
        }
        td p, th p {
            margin: unset;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .clear {
            padding: 10px;
        }
        @media print {
            @page {
                margin: 10px;
            }
            body {
                margin: 50px;
            }
        }
    </style>
    <table>
        <tr>
            <th class="text-center">
                <p>
                    <b>Receiving Receipt</b>
                </p>
            </th>
        </tr>
        <tr>
            <td class="clear">&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td width="20%" class="text-right">Supplier :</td>
                        <td width="40%" class="bbottom"><?php echo ucwords($supplier['supplier_name']); ?></td>
                        <td width="20%" class="text-right">Date :</td>
                        <td width="20%" class="bbottom"><?php echo date("Y-m-d", strtotime($receiving['date_added'])); ?></td>
                    </tr>
                    <tr>
                        <td width="20%" class="text-right">Reference Number :</td>
                        <td width="80%" class="bbottom" colspan="3"><?php echo $receiving['ref_no']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="clear">&nbsp;</td>
        </tr>
        <tr>
            <table>
                <tr>
                    <th class="wborder">Product Name</th>
                    <th class="wborder">Quantity</th>
                    <th class="wborder">Price</th>
                    <th class="wborder">Tax</th>
                </tr>
                <?php 
                while ($row = $inv->fetch_assoc()): 
                    $product = $conn->query("SELECT name FROM product_list WHERE id=" . $row['product_id'])->fetch_assoc();
                    $other_details = json_decode($row['other_details'], true);
                    $price = $other_details['price'];
                    $qty = $row['qty'];
                    $tax = $row['tax'] * 100; // Tax in percentage
                    $total_qty += $qty;
                    $total_price += $price * $qty;
                ?>
                <tr>
                    <td class="wborder"><?php echo $product['name']; ?></td>
                    <td class="wborder text-center"><?php echo $qty; ?></td>
                    <td class="wborder text-right"><?php echo number_format($price, 2); ?></td>
                    <td class="wborder text-center"><?php echo number_format($tax, 2) . '%'; ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <th class="text-right wborder" colspan="1">Total Quantity</th>
                    <th class="text-center wborder"><?php echo $total_qty; ?></th>
                    <th class="wborder" colspan="2"></th>
                </tr>
                <tr>
                    <th class="text-right wborder" colspan="3">Total Price</th>
                    <th class="text-right wborder"><?php echo number_format($total_price, 2); ?></th>
                </tr>
            </table>
        </tr>
        <tr>
            <td class="clear">&nbsp;</td>
        </tr>
        <tr>
            <th>
                <p class="text-center"><i>This is not an official receipt.</i></p>
            </th>
        </tr>
    </table>
</div>

<script>
    var clone = document.querySelector('#print-receiving').cloneNode(true);
    var newWindow = window.open('', '_blank', 'menubar=no,scrollbars=yes,resizable=yes,width=800,height=600');
    newWindow.document.write('<html><head><title>Receiving Receipt</title></head><body>');
    newWindow.document.write(clone.outerHTML);
    newWindow.document.write('</body></html>');
    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
    setTimeout(function () { newWindow.close(); }, 1500);
</script>
