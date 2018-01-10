{extends file='index.tpl'}
{block name=content}
    <div class="container">
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="{$webPath}/images/1.png" alt="">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="{$webPath}/images/2.jpg" alt="">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="{$webPath}/images/3.jpg" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="jumbotron">
            <h1 class="display-3">Úvod</h1>
            <p class="lead">PlayMC je aplikace pro doporučování hudby. Zjistěte, co vám doporučí, stačí se zaregistrovat.</p>
            <p class="lead">
                <a class="btn btn-secondary btn-lg" href="{$webPath}index.php?registration&register" role="button"><i class="fa fa-pencil fa-lg"></i> Zaregistrovat se</a>
            </p>
        </div>
    </div>
{/block}