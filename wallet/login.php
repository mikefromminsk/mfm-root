<!DOCTYPE html>
<html>
<head>
    <?php include_once "head.php" ?>
    <title>Wallet</title>
    <script src="controllers/login.js?<?=time()?>"></script>
</head>
<body style="background-color: #eeeeee" class="col fill" ng-controller="login">
<div class="row align-center-center flex">
    <div class="col inputs" id="login">
        <div class="row align-center-center">
            <h2>Please Login</h2>
        </div>
        <form ng-submit="sendEmailCode()">
            <div class="row">
                <input class="flex" type="text" ng-model="email" required placeholder="Email"
                       ng-disabled="login_requesting || login_success">
                <input class="flex-40" type="submit" value="Send code" ng-disabled="login_requesting || login_success">
            </div>
            <h4 ng-if="login_success">Please check {{email}}</h4>
            <h4 ng-if="login_error" style="color: red">Request error</h4>
        </form>
    </div>
</div>
</body>
</html>