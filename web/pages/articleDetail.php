<?php
/**
 * Created by PhpStorm.
 * User: sayho
 * Date: 2018. 8. 3.
 * Time: PM 5:49
 */
?>

<? include_once $_SERVER['DOCUMENT_ROOT']."/web/inc/header.php"; ?>
<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/WebBoard.php";?>
<?
    $obj = new webBoard($_REQUEST);
    $categoryInfo = $obj->getCategoryInfo();
    $info = $obj->getArticleInfo();
?>
<script>
    $(document).ready(function(){
        var id = "<?=$_REQUEST["articleId"]?>";
        var ajax = new AjaxSender("/route.php?cmd=WebBoard.increaseArticleView", true, "json", new sehoMap().put("id", id));
        ajax.send(function(data){});

        $(".jMod").click(function(){
            location.href = "/web/pages/articleWrite.php?articleId=" + id;
        });

        $(".jDel").click(function(){
            var ajax = new AjaxSender("/route.php?cmd=WebBoard.deleteArticle", false, "json", new sehoMap().put("id", id));
            if(confirm("정말 삭제하시겠습니까?")){
                ajax.send(function(data){
                    if(data.returnCode === 1){
                        alert("삭제되었습니다");
                        location.href = "/web/pages/articleList.php?categoryId=" + "<?=$_REQUEST["categoryId"]?>";
                    }
                });
            }
        });
    });
</script>

<section class="wrapper special books">
    <div class="inner">
        <h5 class="dirHelper">BibleTime 나눔 > 게시판명 > 게시글명</h5>
        <div class="articleWrapper align-left">
            <table class="alt white">
                <tr>
                    <td><?=$info["userName"]?></td>
                    <?if($user->id == $info["customerId"]){?>
                        <td class="smallIconTD" style="text-align:right">
                            <a href="#" class="jMod">수정</a>
                        </td>
                        <td class="smallIconTD" style="text-align:right">
                            <a href="#" class="jDel">삭제</a>
                        </td>

                    <?}?>
                </tr>
            </table>
            <h2 class="nanumGothic"><?=$info["title"]?></h2>
            <p class="nanumGothic">조회 <?=$info["viewCnt"]?>회</p>
            <div class="image fit"><img src="<?=$info["imgPath"] != "" ? $obj->fileShowPath . $info["imgPath"] : ""?>"/></div>
            <h4 class="nanumGothic" style="color:black;"><?=$info["content"]?></h4>
        </div>
    </div>
</section>
<? include_once $_SERVER['DOCUMENT_ROOT']."/web/inc/footer.php"; ?>
