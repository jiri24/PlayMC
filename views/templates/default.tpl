{extends file='index.tpl'}
{block name=content}
    <div class="container">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
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
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
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