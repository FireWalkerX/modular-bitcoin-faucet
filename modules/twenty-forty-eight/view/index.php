<div class="middle left">
    <?= $top_bar ?>
    <h1>2048.</h1>
    <p>Satoshi: {{(score/75)| number:0}}&nbsp;&nbsp;&nbsp;&nbsp;Score: {{score}}</p>
    <div class="twenty-forty-eight" ng-model="score" addr="{{btcAddress}}"></div>
</div>