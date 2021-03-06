<?php
/**
 * Created by PhpStorm.
 * User: sayho
 * Date: 2018. 8. 3.
 * Time: PM 6:06
 */
?>

<? include_once $_SERVER['DOCUMENT_ROOT']."/web/inc/header.php"; ?>
<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/WebUser.php";?>
<? include $_SERVER["DOCUMENT_ROOT"] . "/common/classes/Uncallable.php";?>
<?
    $obj = new webUser($_REQUEST);
    $uc = new Uncallable($_REQUEST);

    $nationCode = $uc->getProperty("CONST_SUPPORT_NATION");
    $continent = $uc->getContinentCode($nationCode);
    $lastSupportNumber = $uc->getLastSupportNumber($nationCode);
    $article = $uc->getSupport($lastSupportNumber, $country_code);
?>
<script>
    $(document).ready(function(){
        $(".jSupport").click(function(){
            var id = $(this).attr("id");
            location.href = "/web/pages/supportDetail.php?id=" + id;
        });
    });
</script>
<div class="image fit" exposureSet="SECTION_SUPPORT_BANNER">
    <img src="<?=$obj->fileShowPath.$CONST_IMAGE["L_IMG_SUPPORT_BANNER"]?>" />
</div>

<section class="wrapper special books" exposureSet="SECTION_SUPPORT_CONTENT">
    <div class="inner">
        <div class="row">
            <!-- Break -->
            <div class="3u 12u$(medium)">
                <h2><?=$article["Title"]?></h2>
            </div>
            <div class="6u 12u$(medium)">
                <p class="align-left">
                    <?=$article["content"]?>
                </p>
                <div class="box alt">
                    <div class="row 50% uniform">
                        <?if($article["imgPath1"] != ""){?>
                            <div class="4u"><span class="image fit"><img src="<?=$obj->fileShowPath.$article["imgPath1"]?>" alt="" /></span></div>
                        <?}?>
                        <?if($article["imgPath2"] != ""){?>
                            <div class="4u"><span class="image fit"><img src="<?=$obj->fileShowPath.$article["imgPath2"]?>" alt="" /></span></div>
                        <?}?>
                        <?if($article["imgPath3"] != ""){?>
                            <div class="4u"><span class="image fit"><img src="<?=$obj->fileShowPath.$article["imgPath3"]?>" alt="" /></span></div>
                        <?}?>
                    </div>
                </div>
            </div>
            <div class="3u$ 12u$(medium)">
                <a href="donationDetail.php?nid=<?=$nationCode?>&id=<?=$lastSupportNumber?>&state=<?=$continent?>">
                    <img class="circleBtn" src="<?=$obj->fileShowPath.$CONST_IMAGE["L_IMG_SUPPORT_BTN_01"]?>" /></a>
                <a href="#" class="jSupport" id="<?=$lastSupportNumber?>"><img class="circleBtn" src="<?=$obj->fileShowPath.$CONST_IMAGE["L_IMG_SUPPORT_BTN_02"]?>" /></a>
            </div>
        </div>
    </div>

</section>

<!-- Three -->
<section class="wrapper special slim" exposureSet="SECTION_SUPPORT_BOTTOM_IMG">
    <h2><?=$SUPPORT_ELEMENTS["phrase"]?></h2>
    <div class="image fit slim">
        <img src="<?=$obj->fileShowPath.$CONST_IMAGE["L_IMG_SUPPORT_BANNER_BOT"]?>" />
    </div>
</section>
<? include_once $_SERVER['DOCUMENT_ROOT']."/web/inc/footer.php"; ?>
