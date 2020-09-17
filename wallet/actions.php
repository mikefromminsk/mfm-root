<!DOCTYPE html>
<html>
<head>
    <?php include_once "head.php" ?>
    <title>Actions</title>
    <script src="controllers/actions.js?<?=time()?>"></script>
</head>
<body style="background-color: #eeeeee" class="col fill" ng-controller="actions">
<div class="row align-center-center flex">
    <div class="col inputs" id="login">
        <div class="row align-center-center">
            <h2>Please Login</h2>
        </div>
        <div class="row table-row" ng-repeat="action in actions">
            <div>{{action.action_id}}</div>
            <div>{{action.user_sender}}</div>
            <div>{{action.action_datetime}}</div>
        </div>
        <button ng-click="update()">Update</button>
        <a href="buy.php"><button>buy</button></a>
    </div>
</div>
</body>
</html>


<style>
    .table-row > * {
        margin-right: 10px;
    }
</style>