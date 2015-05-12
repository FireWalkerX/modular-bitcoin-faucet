<?php
error_reporting(0);
ini_set('display_errors', 0);

if(isset($_GET['ref'])) setcookie('ref', $_GET['ref'], time() + 3600, '/');

require "./lib/FaucetManager.php"; ?>
<html ng-app="btcFaucetApp">
<head>
    <title>All The Satoshi!</title>
    <link href="style.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js" type="application/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-cookies.min.js" type="application/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="js/controller.js" type="application/javascript"></script>
    <?php if(isset($_COOKIE['btcAddress'])) {
        $mgr = new Manager($_COOKIE['btcAddress']);
        ?>
    <?php } ?>
</head>
<body>
<div class="header">
    <a href="./" class="logo"></a>
    <div class="ad">
        <iframe scrolling="no" frameborder="0" src="//adbit.co/adspace.php?a=1F4DU7F5AB57A" style="overflow:hidden;width:728px;height:90px;"></iframe>
    </div>
</div>
<div class="content">
    <div class="ad left"></div>
    <div class="ad right"></div>
    <div class="middle left" ng-controller="MainFaucetCtrl"<?php if(isset($_COOKIE['btcAddress'])) echo 'ng-init="init(' . $mgr->getLastSpin() . ', ' . $mgr->getRemainingTries() . ', {base: ' . $mgr->config["baseAmt"] . ', max: ' . $mgr->config["maxBonusAmt"] . ', chance: ' . $mgr->config["bonusChance"] . '});"'; ?>>
        <?php if(!isset($_COOKIE['btcAddress'])) { ?>
            <form method="post" action="./cgi-bin/login.php">
                <input type="text" name="btcAddress"/>
                <input type="submit" value="Login!"/>
            </form>
        <?php } else { ?>
            <div id="top-bar"><span>Payout by <a href="javascript:void();" ng-click="showCaptcha = true;captchaShowPayout = true;"><b>Paytoshi</b></a> <a href="javascript:void();"></a></span><span>Balance: <b>{{satBalance}}</b> satoshi</span><span id="addr"><b>{{btcAddress}}</b></span></div>
            <?php if(isset($_POST['event'])) { ?>
                <?php if($_POST['event'] == 'paidout' && isset($_POST['error'])) { ?>
                    <div class="notice red">Error: <?= $_POST['error'] ?></div>
                <?php } else if($_POST['event'] == 'paidout') { ?>
                    <div class="notice">You've been sent <?= $_POST['amount'] ?> satoshi on <a ng-href="https://paytoshi.org/{{btcAddress}}/balance">Paytoshi</a>!</div>
                <?php } else if($_POST['event'] == 'satoshiclaimed' && isset($_POST['error'])) { ?>
                    <div class="notice red">Error: <?= $_POST['error'] ?></div>
                <?php } else if($_POST['event'] == 'satoshiclaimed') { ?>
                    <div class="notice"><?= $_POST['amount'] ?> satoshi has been added to your balance!</div>
                <?php } ?>
            <?php } ?>
            <?php if($mgr->getRemainingTries() < 1) { ?>
                Time until next claim
                <div ng-init="timeLeft = <?= $mgr->getWaitTime() ?>;startCountDown();">
                    <h1>{{secondsToStr(timeLeft)}}</h1>
                </div>
            <?php } else { ?>
                Pick a curve: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input id="fractal-formula" type="radio" name="formula" value="fractal" ng-model="formula" ng-disabled="spinningDown"><label for="fractal-formula">Fractal</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input id="radical-formula" type="radio" name="formula" value="radical" ng-model="formula" ng-disabled="spinningDown"><label for="radical-formula">Radical</label>
                <div id="rng-spinner" ng-init="startSpin();">0000</div>
                <div id="rng-value">= 0 satoshi</div>
                <button id="rng-stop" ng-click="stopSpin()" ng-hide="spinningDown">Stop</button>
                <span ng-show="spinDownDone">{{remainingSpins}} tries left</span><br>
                <button id="rng-respin" ng-click="lastSpin = null;number = null;startSpin()" ng-show="spinDownDone && remainingSpins > 0">Try Again</button>
                <button id="rng-claim" ng-click="showCaptcha = true;captchaShowClaim = true;" ng-show="spinDownDone">Claim</button>
            <?php } ?>
            <div id="captcha-container" ng-show="showCaptcha">
                <div class="g-recaptcha" data-sitekey="6LdzugYTAAAAAM8sRyvVKcj_uyqKefdzNLnYZx3i"></div>
                <button id="rng-claim" ng-click="claimSpin()" ng-show="captchaShowClaim">Claim</button>
                <button id="rng-claim" ng-click="payout()" ng-show="captchaShowPayout">Payout</button>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>