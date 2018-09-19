<? include_once $_SERVER['DOCUMENT_ROOT']."/admin/inc/header.php"; ?>
<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/Uncallable.php";?>
<?
    $obj = new Uncallable($_REQUEST);
    $list = $obj->getTransList();
?>

<script>
    $(document).ready(function(){
        $(".jPage").click(function(){
            var page = $(this).attr("page");
            reloadPage(page);
        });

        $(".jView").click(function(){
            location.href = "/admin/pages/customerManage/transactionDetailView.php";
        });

        $(".jTab").click(function(){
            var target = $(this).attr("target");
            $("[name=type]").val(target);
            form.submit();
        });

        $(".jOpt").change(function(){
            reloadPage($("#page").val());
        });

        $(".jExcel").click(function(){
            exportToExcel();
        });

        function exportToExcel(){
            var divToPrint=document.getElementById("toPrint");
            var optionCss = "";//"#toPrint{width : 210mm;}";
            var htmls = $("#styleCss").prop("outerHTML") + "<style>" + optionCss + "</style>" + divToPrint.outerHTML;

            var uri = 'data:application/vnd.ms-excel;base64,';
            var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta charset="utf-8"></head><body><table>{table}</table></body></html>';
            var base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            };

            var format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            };

//            htmls = "YOUR HTML AS TABLE"

            var ctx = {
                worksheet : 'Worksheet',
                table : htmls
            }

            var link = document.createElement("a");
            link.download = "export.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();
        }

        function reloadPage(page){
            var year = $("#jYear").val();
            var month = $("#jMonth").val();
            var memType = $("#memType").val();
            location.href = "/admin/pages/customerManage/transactionDetailsSend.php?" +
                "year=" + year + "&" +
                "month=" + month + "&" +
                "type=" + memType + "&" +
                "page=" + page;
        }

        if("<?=$_REQUEST["type"]?>" == "" || "<?=$_REQUEST["year"]?>" == "" || "<?=$_REQUEST["month"]?>" == ""){
            reloadPage($("#page").val());
        }

    });
</script>

<input type="hidden" id="page" value="<?=$_REQUEST["page"] == "" ? "1" : $_REQUEST["page"]?>" />

<div id="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a>고객 관리</a>
            </li>
            <li class="breadcrumb-item active">거래명세서 발송</li>
        </ol>

        <form id="form">
            <input type="hidden" name="type" value="<?=$_REQUEST["type"]?>"/>
            <input type="hidden" name="customerType" value="<?=$_REQUEST["customerType"]?>"/>
            <input type="hidden" name="month" value="<?=$_REQUEST["month"]?>"
        </form>

        <div class="float-right">
            <button type="button" class="btn btn-secondary mb-2 jTranscendancePrint">인쇄</button>
            <button type="button" class="btn btn-secondary mb-2 jEmail">Email</button>
            <button type="button" class="btn btn-secondary mb-2 jTranscendanceExcel">Excel</button>
        </div>

        <select class="custom-select jOpt" id="memType" style="width: 20%">
            <option value="A" <?=$_REQUEST["type"]=="A" ? "SELECTED" : ""?>>전체</option>
            <option value="P" <?=$_REQUEST["type"]=="P" ? "SELECTED" : ""?>>개인</option>
            <option value="T" <?=$_REQUEST["type"]=="T" ? "SELECTED" : ""?>>단체</option>
        </select>
        <select class="custom-select jOpt" id="jYear" style="width: 20%">
            <?for($e = 1950; $e < intval(date("Y")) + 50; $e++){?>
                <option value="<?=$e?>" <?=$e == intval($_REQUEST["year"]) ? "SELECTED" : ""?>><?=$e?>년</option>
            <?}?>
        </select>
        <select class="custom-select jOpt" id="jMonth" style="width: 20%">
            <?for($e = 1; $e <= 12; $e++){?>
                <option value="<?=$e < 10 ? "0".$e : $e?>" <?=$e == intval($_REQUEST["month"]) ? "SELECTED" : ""?>><?=$e < 10 ? "0".$e : $e?>월</option>
            <?}?>
        </select>

        <br/>
        <br/>

        <table border=1 class="table table-bordered" id="toPrint">
            <thead>
            <tr>
                <th width="20px">-</th>
                <th width="25px">No.</th>
                <th>거래처명</th>
                <th>품명 및 규격</th>
                <th>금액</th>
                <th>비고</th>
                <th width="80px">인쇄</th>
            </tr>
            </thead>
            <tbody>
            <?
            $vnum = $obj->virtualNum;
            foreach($list as $item){?>
                <tr>
                    <td>
                        <input class="form-control-sm" type="checkbox" sid="<?=$item["id"]?>" />
                    </td>
                    <td><?=$vnum--?></td>
                    <td><?=$item["rName"]?></td>
                    <td><?=$item["pbName"]?></td>
                    <td><?=$item["totalPrice"]?></td>
                    <?
                        $tempCursor = 0;
                        $tempArr = array();
                    if($item["subType"] == "2") $tempArr[$tempCursor++] = "묶음배송";
                    if($item["cm"][0] == "1") $tempArr[$tempCursor++] = "1도";
                    if($item["cm"][1] == "1") $tempArr[$tempCursor++] = "2도";
                    if($item["cm"][2] == "1") $tempArr[$tempCursor++] = "3도";
                    if($item["cm"][3] == "1") $tempArr[$tempCursor++] = "4도";
                    $toShow = implode("/", $tempArr);
                    ?>
                    <td><?=$toShow?></td>
                    <td>
                        <button type="button" class="btn-secondary mb-2 jPrint btn-sm" sid="<?=$item["id"]?>">인쇄</button>
                    </td>
                </tr>
            <?}?>
            </tbody>
        </table>

        <?include $_SERVER["DOCUMENT_ROOT"] . "/admin/inc/pageNavigator.php";?>

    </div>
</div>



<? include_once $_SERVER['DOCUMENT_ROOT']."/admin/inc/footer.php"; ?>

