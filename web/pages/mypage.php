<?php
/**
 * Created by PhpStorm.
 * User: sayho
 * Date: 2018. 8. 3.
 * Time: PM 6:30
 */
?>

<? include_once $_SERVER['DOCUMENT_ROOT']."/web/inc/header.php"; ?>
<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/WebUser.php";?>
<?
    $obj = new WebUser($_REQUEST);
    $info = $obj->customerInfo();

    $userInfo = $info["userInfo"];
    $subscriptionInfo = $info["subscriptionInfo"];
    $supportInfo = $info["supportInfo"];
    $paymentInfo = $info["paymentInfo"];

    if($_COOKIE["btLocale"] == "kr") {
        $currency = "₩";
        $decimal = 0;
    }
    else{
        $currency = "$";
        $decimal = 2;
    }
?>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<style>
    .ui-datepicker select{display: inline!important;}
</style>

<script>
    $(document).ready(function(){
        var id = "<?=$userInfo["id"]?>";
        var type = "<?=$userInfo["type"]?>";
        var locale = "<?=$_COOKIE["btLocale"]?>";
        $(".datepicker").datepicker({
            yearRange: "-100:",
            showMonthAfterYear:true,
            inline: true,
            changeMonth: true,
            changeYear: true,
            dateFormat : 'yy-mm-dd',
            dayNamesMin:['일', '월', '화', '수', '목', '금', ' 토'],
            monthNames:['1월','2월','3월','4월','5월','6월','7 월','8월','9월','10월','11월','12월'],
            monthNamesShort:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월']
        });

        $(".jAddress").click(function(){
            new daum.Postcode({
                oncomplete: function(data){
                    console.log(data);
                    $("[name=zipcode]").val(data.zonecode);
                    $("[name=addr]").val(data.address);
                }
            }).open();
        });

        $(".jSave").click(function(){
            var currentPass = $("#userPW").val();
            var newPW = $("#newPW").val();
            var newPWC = $("#newPWC").val();

            if(newPW != null && newPW != "" && verifyPassword(newPW) === false){
                alert("비밀번호 형식에 맞춰서 작성해 주시기 바랍니다.");
                return;
            }

            var ajax = new AjaxSender("/route.php?cmd=WebUser.checkCustomerPassword", true, "json", new sehoMap().put("id", id).put("password", currentPass));
            ajax.send(function(data){
                if(data.returnCode !== 1){
                    alert("현재 비밀번호가 일치하지 않습니다.");
                    return;
                }
                else{
                    if(newPW != null && newPW !== newPWC){
                        alert("새로운 비밀번호가 일치하지 않습니다.");
                        return;
                    } else saveOperation();
                }
            });
        });

        function saveOperation(){
            var password = $("#newPW").val();
            var phone = $("#phone").val();
            var zipcode = $("#zipcode").val();
            var addr = $("#addr").val();
            var addrDetail = $("#addrDetail").val();
            var cName = $("#cName").val();
            var cPhone = $("#cPhone").val();
            var notiFlag = $("[name=notiFlag]:checked").val();
            var birth = $("#birth").val();
            var params = new sehoMap().put("id", id).put("password", password).put("phone", phone).put("zipcode", zipcode).put("addr", addr)
                .put("addrDetail", addrDetail).put("cName", cName).put("cPhone", cPhone).put("notiFlag", notiFlag).put("birth", birth);
            params.put("type", type);

            var ajax = new AjaxSender("/route.php?cmd=WebUser.updateCustomerInfo", false, "json", params);
            ajax.send(function(data){
                if(data.returnCode === 1){
                    alert("저장되었습니다");
                    location.reload();
                }
                else{
                    alert(data.returnMessage);
                    location.reload();
                }
            });
        }

        $(".jCancel").click(function(){
            history.back();
        });

        $(".jInquire").click(function(){
            location.href = "/web/pages/inquire.php";
        });

        $(".jView").click(function(){
            location.href = "/web/pages/mySubscription.php";
        });

        if(locale !== "kr"){
            $(".jAddress").hide();
            $("[name=addr]").attr("readonly", false);
            $("[name=zipcode]").attr("readonly", false);
        }
    });
</script>

<section class="wrapper special books">
    <div class="inner mypage">
        <header>
            <h2 class="pageTitle"><?=$MYPAGE_ELEMENTS["title"]?></h2>
            <div class="empLineT"></div>
            <p><?=$MYPAGE_ELEMENTS["subTitle"]?></p>
        </header>

        <div style="" class="align-right">
<!--            <a href="#" class="grayButton roundButton">거래명세서</a>-->
            <a href="#" class="grayButton roundButton jInquire">1:1 문의</a>
        </div>
        <hr />

        <div class="row">
            <div class="4u 12u$(small)">
                <h2 class="nanumGothic">ID/PW</h2>
            </div>
            <div class="8u$ 12u$(small) align-left">
                <h3 style="color:black;" class="nanumGothic" ><?=$userInfo["email"]?></h3>
                <input class="smallTextBox" type="password" name="userPW" id="userPW" placeholder="<?=$MYPAGE_ELEMENTS["input"]["cPass"]?>" />
                <input class="smallTextBox" type="password" name="newPW" id="newPW" placeholder="<?=$MYPAGE_ELEMENTS["input"]["nPass"]?>" />
                <input class="smallTextBox" type="password" name="newPWC" id="newPWC" placeholder="<?=$MYPAGE_ELEMENTS["input"]["nPassConfirm"]?>" />
                <h5><?=$MYPAGE_ELEMENTS["input"]["text"]?></h5>
            </div>

            <?if($userInfo["type"] == "1"){?>
                <div class="4u 12u$(small)">
                    <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["ordinary"]?></h2>
                </div>
                <div class="8u$ 12u$(small) align-left">
                    <h3 style="color:black;" class="nanumGothic" ><?=$userInfo["name"]?></h3>
                    <input class="smallTextBox" type="text" name="phone" id="phone" placeholder="<?=$MYPAGE_ELEMENTS["input"]["phone"]?>" value="<?=$userInfo["phone"]?>"/>

                    <input class="smallTextBox" type="text" name="zipcode" id="zipcode" placeholder="<?=$MYPAGE_ELEMENTS["input"]["zip"]?>" value="<?=$userInfo["zipcode"]?>" readonly/>
                    <a href="#" class="grayButton roundButton innerButton jAddress">주소찾기</a>g
                    <input class="smallTextBox" type="text" name="addr" id="addr" placeholder="<?=$MYPAGE_ELEMENTS["input"]["addr"]?>" value="<?=$userInfo["addr"]?>" readonly/>
                    <input class="smallTextBox" type="text" name="addrDetail" id="addrDetail" placeholder="<?=$MYPAGE_ELEMENTS["input"]["addrDetail"]?>" value="<?=$userInfo["addrDetail"]?>" />
                    <input class="smallTextBox datepicker" type="text" name="birth" id="birth" placeholder="<?=$MYPAGE_ELEMENTS["input"]["birth"]?>" value="<?=$userInfo["birth"]?>" />
                </div>
            <?}else if($userInfo["type"] == "2"){?>
                <div class="4u 12u$(small)">
                    <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["church"]?></h2>
                </div>
                <div class="8u$ 12u$(small) align-left">
                    <input class="smallTextBox" type="text" name="cName" id="cName" placeholder="교회/단체명" value="<?=$userInfo["cName"]?>" />
                    <input class="smallTextBox" type="text" name="cPhone" id="CPhone" placeholder="교회/단체 전화번호" value="<?=$userInfo["cPhone"]?>" />
                    <input class="smallTextBox" type="text" name="zipcode" id="zipcode" placeholder="우편번호" value="<?=$userInfo["zipcode"]?>" readonly/>
                    <a href="#" class="grayButton roundButton innerButton jAddress">주소찾기</a>
                    <input class="smallTextBox" type="text" name="addr" id="addr" placeholder="주소" value="<?=$userInfo["addr"]?>" readonly/>
                    <input class="smallTextBox" type="text" name="addrDetail" id="addrDetail" placeholder="상세주소" value="<?=$userInfo["addrDetail"]?>" />
                </div>

                <div class="4u 12u$(small)">
                    <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["charge"]?></h2>
                </div>
                <div class="8u$ 12u$(small) align-left">
                    <input class="smallTextBox" type="text" name="name" id="name" placeholder="담당자 성함" value="<?=$userInfo["name"]?>"/>
                    <input class="smallTextBox" type="text" name="rank" id="rank" placeholder="담당자 직분" value="<?=$userInfo["rank"]?>"/>
                    <input class="smallTextBox" type="text" name="phone" id="phone" placeholder="휴대폰 번호" value="<?=$userInfo["phone"]?>"/>
                </div>
            <?}?>

            <div class="4u 12u$(small)">
                <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["noti"]?></h2>
            </div>
            <div class="8u$ 12u$(small) align-left" style="margin-top : 1em;">
                <input type="radio" id="noti_on" name="notiFlag" value="1" <?=$userInfo["notiFlag"] == "1" ? "checked" : ""?>>
                <label for="noti_on">On</label>
                <input type="radio" id="noti_off" name="notiFlag" value="0" <?=$userInfo["notiFlag"] == "0" ? "checked" : ""?>>
                <label for="noti_off">Off</label>
            </div>

            <div class="4u 12u$(small)">
                <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["payMethod"]?></h2>
            </div>
            <div class="8u$ 12u$(small) align-left">
<!--                <h3 class="nanumGothic">카드사/계좌이체/직접입금 &nbsp;&nbsp; 카드번호 앞 네자리</h3>-->
                <table>
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>결제유형</th>
                        <th>카드번호/계좌번호</th>
                        <th>카드사/은행</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?for($i=0; $i<sizeof($paymentInfo); $i++){?>
                        <tr>
                            <td><?=$i+1?></td>
                            <td>
                                <?
                                    switch($paymentInfo[$i]["pmType"]){
                                        case "CC":
                                            echo "신용카드";
                                            break;
                                        case "BA":
                                            echo "계좌이체";
                                            break;
                                        case "FC":
                                            echo "해외신용카드";
                                            break;
                                    }
                                ?>
                            </td>
                            <td><?=$paymentInfo[$i]["info"]?></td>
                            <td><?=$paymentInfo[$i]["cardTypeDesc"] . $paymentInfo[$i]["bankTypeDesc"]?></td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>

            <div class="4u 12u$(small)">
                <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["subscription"]?></h2>
            </div>
            <div class="8u$ 12u$(small) align-left">
                <a href="#" class="jView" style="float:right">상세페이지 보기</a>
                <table>
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>받는 분</th>
                        <th>버전</th>
                        <th>수량</th>
                        <th>배송정보</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?for($i=0; $i<sizeof($subscriptionInfo); $i++){?>
                        <tr>
                            <td><?=$i+1?></td>
                            <td>
                                <?=$subscriptionInfo[$i]["rName"] == "" ? $user->name : $subscriptionInfo[$i]["rName"]?>
                            </td>
                            <td><?=$subscriptionInfo[$i]["publicationName"]?></td>
                            <td><?=$subscriptionInfo[$i]["cnt"]?></td>
                            <td>
                                <?
                                    switch($subscriptionInfo[$i]["deliveryStatus"]){
                                        case 0:
                                            echo "정상";
                                            break;
                                        case 1:
                                            echo "취소";
                                            break;
                                        case 2:
                                            echo "발송대기중";
                                            break;
                                    }
                                ?>
                            </td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>

            <div class="4u 12u$(small)">
                <h2 class="nanumGothic"><?=$MYPAGE_ELEMENTS["menu"]["support"]?></h2>
            </div>
            <div class="8u$ 12u$(small) align-left">
                <table>
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>후원자명</th>
                        <th>후원국가</th>
                        <th>시작한 날짜</th>
                        <th>금액</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?for($i=0; $i<sizeof($supportInfo); $i++){?>
                        <tr>
                            <td><?=$i+1?></td>
                            <td><?=$supportInfo[$i]["rName"]?></td>
                            <td><?=$supportInfo[$i]["nation"]?></td>
                            <td><?=$supportInfo[$i]["regDate"]?></td>
                            <td><?=number_format($supportInfo[$i]["totalPrice"], $decimal)?></td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="#" class="roundButton grayButton jSave">저장</a>
    </div>
</section>
<? include_once $_SERVER['DOCUMENT_ROOT']."/web/inc/footer.php"; ?>
