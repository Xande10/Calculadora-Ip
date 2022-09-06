<?php
    require_once('database.class.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>IP de Rede</title>
</head>
<body>
    <div>
        <center>
            <section>
                <h1>Calcular a máscara de sub-rede IPv4:</h1>
            </section>
            <form method="POST">
                <b>IP/CIDR</b><br>
                <input id="text" type="text" name="ip" value="<?php echo @$_POST['ip'];?>">
                <input class="btn first" style="cursor: pointer;" type="submit" value="Calcular">
            </form>            
                <?php
                    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['ip'] ) ) { //Verifica se o formulário enviado
                        //previne erros do F5 resubmit
                        $ip = new IpCalculator($_POST['ip']);
                        
                        // verifica se o IP é váildo
                        if( $ip->valida_endereco() ) {
                            echo $ip;
                        }
                            // caso não for, ele retorna uma mensagem 
                            else {
                                echo 'Endereço IPv4 inválido!';
                            }   
                        $ip = IpCalculator::CatchIP();
                        echo "<b>IP da máquina:  </b>". $ip . '<br>';
                    } 
                ?>
        </center>
    </div>
</body>
</html>