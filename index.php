<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form onclick="event.preventDefault()">
        <label>Ваше имя:</label><input type="text" required class="amoCRM"></input><br><br>
        <label>Ваш e-mail:</label><input type="email" required class="amoCRM"></input><br><br>
        <label>Ваш телефон:</label><input type="tel" required class="amoCRM"></input><br><br>
        <label>Цена:</label><input type="number" required class="amoCRM"></input><br><br>
        <input type="submit" class="otpravka" onclick="send()"></input>
    </form>
    
 
<script>
    function send () {
    var data = [];    
    var amoCRM = document.querySelectorAll(".amoCRM");
    for (var value of amoCRM) {
    if (value.value == '') {
    alert("Заполните все поля");
    return;
    }else    
    data.push(value.value);
}
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/amoCRM/amo.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data='+data);
    }
</script>    
</body>
</html>