<?php
/**
 * Created by PhpStorm.
 * User: sayho
 * Date: 2018. 8. 8.
 * Time: PM 5:09
 */
?>

<? include_once $_SERVER['DOCUMENT_ROOT']."/admin/inc/header.php"; ?>
<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/Uncallable.php";?>
<?
    $obj = new Uncallable($_REQUEST);
    $item = $obj->getNotice();
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function(){
        resizeTextArea();

        $("[name=imgFile]").change(function(){
            readURL(this, ".jImg");
            $("#imgPath").val("");
        });

        function resizeTextArea(){
            var arr = $("textarea");
            for(var e = 0; e < arr.length; e++){
                arr.eq(e).height(arr.eq(e).prop('scrollHeight'));
            }
        }

        function readURL(input, selector){
            if (input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $(selector).attr("src", e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".jSave").click(function(){
            if(confirm("저장하시겠습니까?")){
                var ajax = new AjaxSubmit("/route.php?cmd=Uncallable.upsertNotice", "post", true, "json", "#form");
                ajax.send(function(data){
                    if(data.returnCode === 1){
                        alert("저장되었습니다.");
                        location.href = "/admin/pages/staffService/noticeList.php";
                    }
                });
            }
        });

        $(".jCancel").click(function(){
            history.back();
        });
    });
</script>

<div id="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a>직원서비스</a>
            </li>
            <li class="breadcrumb-item ">공지사항</li>
            <li class="breadcrumb-item active">공지사항 상세</li>
        </ol>

        <div class=" float-right">
            <button type="button" class="btn btn-secondary mb-2 jSave">저장</button>
            <button type="button" class="btn btn-danger mb-2 jCancel">취소</button>
        </div>

        <form method="post" id="form" action="#" enctype="multipart/form-data">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">제목</span>
                </div>
                <input type="hidden" name="id" value="<?=$item["id"]?>" />
                <input type="text" class="form-control" name="title" value="<?=$item["title"]?>">
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">내용</span>
                </div>
                <textarea class="form-control" name="content"><?=$item["content"]?></textarea>
            </div>

            <div style="text-align: center;">
                <img class="jImg" src="<?=$item["filePath"] != "" ? $obj->fileShowPath . $item["filePath"] : ""?>" width="100px;"/>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3">첨부파일</span>
                </div>
                <div class="custom-file">
                    <input type="hidden" name="filePath" value="<?=$item["filePath"]?>"/>
                    <input type="file" class="custom-file-input" name="imgFile" id="inputGroupFile01">
                    <label class="custom-file-label jLabel" for="inputGroupFile01"><?=$item["fileName"] == "" ? "파일을 선택하세요" : $item["fileName"]?></label>
                </div>
            </div>
        </form>
        
    </div>
</div>

<? include_once $_SERVER['DOCUMENT_ROOT']."/admin/inc/footer.php"; ?>
