<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>First PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php /*$hi = 'World';*/ ?>
    <?php $hi = $_GET['hi']; ?>
    <h2>Hello <?php echo $hi ?>!</h2>
</body>
</html>