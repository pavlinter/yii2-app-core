<?php
use app\assets_b\AppAsset;
use app\core\adm\models\Page;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */
$appAsset = AppAsset::register($this);

$menus = Page::find()->with(['translations','childs'])->where(['id_parent' => [1,2], 'active' => 1, 'visible' => 1])->orderBy(['weight' => SORT_ASC])->all();
$Menu1 = [];
$Menu2 = [];

$langBegin = Yii::$app->getUrlManager()->langBegin;
if (isset($langBegin['0']) && $langBegin['0'] === Yii::$app->language) {
    $baseUrl = Url::to('/');
} else {
    $baseUrl = Url::to('/' . Yii::$app->language . '/');
}


foreach ($menus as $menu) {
    $item = [];
    $item['label'] = $menu->name;
    if ($menu->type === 'main') {
        $item['url'] = $baseUrl;
    } else {
        $item['url'] = $menu->url();
    }
    if ($menu->childs) {
        foreach ($menu->childs as $child) {
            $item['items'][] = [
                'label' => $child->name,
                'url' => $child->url(),
            ];
        }
    }
    if ($menu->id_parent == 1) {
        $Menu1[] = $item;
    } elseif($menu->id_parent == 2) {
        $Menu2[] = $item;
    }
}

if (Yii::$app->user->isGuest) {
    $Menu1[] = [
        'label' => 'Login',
        'url' => ['/site/login']
    ];

    $Menu1[] = [
        'label' => 'Sign up',
        'url' => ['/site/signup']
    ];
    $Menu1[] = [
        'label' => 'Reset Password',
        'url' => ['/site/request-password-reset']
    ];


} else {
    $Menu1[] = [
        'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post']
    ];




}

$MenuLangs = [];
foreach (Yii::$app->getI18n()->getLanguages() as $language) {
    if ($language['image']) {
        $text = Html::img($language['image']);
    } else {
        $text = Html::tag('span', $language['code'], ['class' => 'lang-code']);
    }
    $url = ['', 'lang' => $language['code']];
    if (isset($language['url'])) {
        $url = $language['url'];
    }

    $MenuLangs[] = [
        'label' => $text,
        'url' => $url,
    ];
}
?>

<?php $this->beginContent('@webroot/views/layouts/base.php'); ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse',
        ],
    ]);

        /*echo \yii\widgets\Menu::widget([
            'options' => ['class' => 'pull-right'],
            'items' => $MenuLangs,
            'encodeLabels' => false,
        ]);*/

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $Menu1,
        ]);

    NavBar::end();
    ?>
</header>

<div class="<?= Yii::$app->params['html.wrapperClass'] ?> container">
    <?= Breadcrumbs::widget([
        'links' => Yii::$app->params['breadcrumbs'],
    ]) ?>

    <?= \app\widgets\Alert::widget() ?>

    <?= $content ?>
</div>

<footer class="footer">
    <div class="container">

    </div>
</footer>

<?php $this->endContent(); ?>

