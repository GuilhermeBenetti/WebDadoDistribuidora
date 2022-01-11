<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once './conexao.php';
require './lib/vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
        <link rel="stylesheet" href="assets/tether/tether.min.css">
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/socicon/css/styles.css">
        <link rel="stylesheet" href="assets/theme/css/style.css">
        <link rel="stylesheet" href="assets/mobirise/css/mbr-additional.css" type="text/css">
        <link rel="shortcut icon" href="assets/images/icone-128x159.png" type="image/x-icon">
        <title>Pre-Cadastro - DD</title>
    </head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
 
    <script type="text/javascript">
    $("#telefone").mask("(00) 0000-0000");
    </script>
    <script type="text/javascript">
    $("#celular").mask("(00) 00000-0000");
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.js"></script>
        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($data['SendAddMsg'])) {
            $attachment = $_FILES['attachment'];
            //var_dump($data);
            //var_dump($attachment);
            $query_msg = "INSERT INTO cadastro_clientes (cnpj, razao, fantasia, data_ab, status, cep, logradouro, numero, bairro, cidade, uf, vendedor, email, telefone, celular, mensagem)
             VALUES (:cnpj, :razao, :nome, :data_ab, :status, :cep, :logradouro, :numero, :bairro, :cidade, :uf, :vendedor, :email, :telefone, :celular, :mensagem)";
            $add_msg = $conn->prepare($query_msg);

            $add_msg->bindParam(':cnpj', $data['cnpj'], PDO::PARAM_STR);
            $add_msg->bindParam(':razao', $data['razao'], PDO::PARAM_STR);
            $add_msg->bindParam(':fantasia', $data['fantasia'], PDO::PARAM_STR);
            $add_msg->bindParam(':data_ab', $data['data_ab'], PDO::PARAM_STR);
            $add_msg->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $add_msg->bindParam(':cep', $data['cep'], PDO::PARAM_STR);
            $add_msg->bindParam(':logradouro', $data['logradouro'], PDO::PARAM_STR);
            $add_msg->bindParam(':numero', $data['numero'], PDO::PARAM_STR);
            $add_msg->bindParam(':bairro', $data['bairro'], PDO::PARAM_STR);
            $add_msg->bindParam(':cidade', $data['cidade'], PDO::PARAM_STR);
            $add_msg->bindParam(':uf', $data['uf'], PDO::PARAM_STR);
            $add_msg->bindParam(':vendedor', $data['vendedor'], PDO::PARAM_STR);
            $add_msg->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $add_msg->bindParam(':telefone', $data['telefone'], PDO::PARAM_STR);
            $add_msg->bindParam(':celular', $data['celular'], PDO::PARAM_STR);
            $add_msg->bindParam(':mensagem', $data['mensagem'], PDO::PARAM_STR);

            $add_msg->execute();

            if ($add_msg->rowCount()) {
                $mail = new PHPMailer(true);
                try {
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host = 'smtp.kinghost.net';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dado@dadotintas.com.br';
                    $mail->Password = 'dadosalau82';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    //Enviar e-mail para o colaborador da empresa
                    $mail->setFrom('fichaprecadastro@dadodistribuidora.com.br', 'Atendimento');
                    $mail->addAddress('guilhermeti@tintasmega.ind.br', 'Administrador');
                    
                    if(isset($attachment['name']) AND !empty($attachment['name'])){
                        $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
                    }

                    $mail->isHTML(true);
                    $mail->Subject = $data['vendedor']. " - " .$data['fantasia'];
                    $mail->Body = "CNPJ: " . $data['cnpj']."<br>Razão Social: " . $data['razao']."<br>Nome Fantasia: " . $data['fantasia']."<br>Data de Abertura: " . $data['data_ab'].";
                    <br>Situação: " . $data['status']."<br>CEP: " . $data['cep']."<br>Logradouro: " . $data['logradouro']."<br>Número: " . $data['numero']."<br>Bairro: " . $data['bairro']."
                    <br>Cidade: " . $data['cidade']."<br>UF: " . $data['uf']."<br>Vendedor: " . $data['vendedor']."<br>Email: " . $data['email']."<br>Telefone: " . $data['telefone']."
                    <br>Celular: " . $data['celular']."<br>Mensagem: " . $data['Mensagem'];
                    $mail->AltBody = "CNPJ: " . $data['cnpj']."\nRazão Social: " . $data['razao']."\nNome Fantasia: " . $data['fantasia']."\nData de Abertura: " . $data['data_ab'].";
                    \nSituação: " . $data['status']."\nCEP: " . $data['cep']."\nLogradouro: " . $data['logradouro']."\nNúmero: " . $data['numero']."\nBairro: " . $data['bairro']."
                    \nCidade: " . $data['cidade']."\nUF: " . $data['uf']."\nVendedor: " . $data['vendedor']."\nEmail: " . $data['email']."\nTelefone: " . $data['telefone']."
                    \nCelular: " . $data['celular']."\nMensagem: " . $data['Mensagem'];

                    $mail->send();
                    unset($data);
                    echo "E-mail enviado com sucesso! Aguarde até que seu cadastro seja finalizado<br>";                    
                } catch (Exception $e) {
                    echo "Erro: Mensagem de contato não enviada com sucesso!<br>";
                }
            } else {
                echo "Erro: Mensagem de contato não enviada com sucesso!<br>";
            }
        }
        ?>
    <body>
        <header>
            <div class="row justify-content-center align-items-center" style="background-color: #DD3C3C">
                    <div class="thumbnail text-center">
                    <img src="assets/images/LOGO DD.png" class="img-responsive">
                    </div>
              </div>
                    <div class="text-center mt-3">
                    <h3>Ficha de Pre-Cadastro de Clientes</h3>
                   </div>
        </header>
 
        <br>
        <form name="add_msg" action="" method="POST" enctype="multipart/form-data">
           <div class="form-row justify-content-center align-items-center">
           <div class="form-group col-md-8">
                   <label>CNPJ:</label>
                   <input type="text" name="cnpj" id="cnpj" data-mask="00.000.000/0000-00" onblur="checkCnpj(this.value)" placeholder="Digite o CNPJ aqui" class="form-control" required>
               </div>
           </div>
               <div class="form-row justify-content-center align-items-center">
                    <div class="form-group col-md-4">
                   <label>Razao Social:</label>
                   <input type="text" name="razao" id="razao" class="form-control" disabled>
                   </div>
                   <div class="form-group col-md-4">
                   <label>Nome Fantasia:</label>
                   <input type="text" name="nome" id="nome" class="form-control" disabled>
                   </div>
               </div>
               <div class="form-row justify-content-center align-items-center">
                    <div class="form-group col-md-3">
                   <label>Abertura:</label>
                   <input type="text" name="data_ab" id="data_ab" class="form-control" disabled>
                   </div>
                   <div class="form-group col-md-2">
                   <label>Situacao:</label>
                   <input type="text" name="status" id="status" class="form-control" disabled>
                   </div>
                   <div class="form-group col-md-3">
                   <label>CEP:</label>
                   <input type="text" name="cep" id="cep" class="form-control" disabled>
                   </div>
               </div>
                   <div class="form-row justify-content-center align-items-center">
                   <div class="form-group col-md-7">
                   <label>Logradouro:</label>
                   <input type="text" name="logradouro" id="logradouro" class="form-control" disabled>
               </div>
                   <div class="form-group col-md-1">
                   <label>Numero:</label>
                   <input type="text" name="numero" id="numero" class="form-control" disabled>
                   </div>
               </div>
               <div class="form-row justify-content-center align-items-center">
                    <div class="form-group col-md-3">
                   <label>Bairro:</label>
                   <input type="text" name="bairro" id="bairro" class="form-control" disabled>
                   </div>
                   <div class="form-group col-md-4">
                   <label>Municipio:</label>
                   <input type="text" name="cidade" id="cidade" class="form-control" disabled>
                   </div>
                   <div class="form-group col-md-1">
                   <label>UF:</label>
                   <input type="text" name="uf" id="uf" class="form-control" disabled>
                   </div>
               </div>
                   <div class="form-row justify-content-center">
                   <div class="form-group col-md-3">
                   <label for="vendedor">Vendedor:</label>
                   <select id="vendedor" name="vendedor" class="form-control" required>
                          <option value="">Selecione o vendedor...</option>
                          <option value="789">789 - Anderson Moutinho</option>
                          <option value="108">108 - Cicero Aparecido</option>
                          <option value="195">195 - Edivaldo</option>
                          <option value="114">114 - João Alvino</option>
                          <option value="629">629 - Leonardo Nogueira</option>
                          <option value="941">941 - Patrick Anderson</option>
                          <option value="827">827 - Paulo Henrique Cambiaghi</option>
                          <option value="841">841 - Paulo Henrique Moutinho</option>
                   </select>
                   </div>
               <div class="col-md-5"></div>
               </div>
                   <div class="form-row justify-content-center align-items-center">
                   <div class="form-group col-md-4">
                   <label>E-Mail:</label>
                   <input type="text" name="email" id="email" class="form-control" placeholder="Digite o e-mail aqui" required>
                   </div>
                   <div class="form-group col-md-2">
                   <label>Telefone:</label>
                   <input type="text" name="telefone" id="telefone" class="form-control" placeholder="Digite o telefone aqui" required>
                   </div>
                   <div class="form-group col-md-2">
                   <label>Celular:</label>
                   <input type="text" name="celular" id="celular" class="form-control" placeholder="Digite o celular aqui" required>
                   </div>
               </div>
                   <div class="form-row justify-content-center align-items-center">
                   <div class="form-group col-md-8">
                   <label>Informacoes Adicionais:</label>
                   <textarea name="mensagem" type="text" class="form-control" placeholder="Digite as informacoes adicionais..." rows="4" required></textarea>
               </div>
               </div>
               <div class="form-row justify-content-center align-items-center">
                   <div class="col-md-2 from-group">
                   <label>Anexo</label>
                   <input type="file" name="attachment" id="attachment">
                   </div>
                </div>
               <div class="text-center">
               <input name="SendAddMsg" type="submit" value="Enviar" class="btn btn-outline-success">
           </div>
               
           </div>
         
       </form>
 
       <script>
 
           function checkCnpj(cnpj){
               $.ajax({
                   'url' : 'https://www.receitaws.com.br/v1/cnpj/' + cnpj.replace(/{^0-9}/g, ''),
                   'type' : "GET",
                   'dataType' : 'jsonp',
                   'success' : function(dado){
                       if(dado.nome == undefined){
                           alert(dado.status + ' ' + dado.message);
                       }else{
                           document.getElementById('razao').value = dado.nome;
document.getElementById('nome').value = dado.fantasia;
document.getElementById('data_ab').value = dado.abertura;
document.getElementById('status').value = dado.situacao;
document.getElementById('cep').value = dado.cep;
document.getElementById('logradouro').value = dado.logradouro;
document.getElementById('numero').value = dado.numero;
document.getElementById('bairro').value = dado.bairro;
document.getElementById('cidade').value = dado.municipio;
document.getElementById('uf').value = dado.uf;
                       }
                       console.log(dado);
                   }
               });
           }
 
 
       </script>
   </body>
   <footer class="text-center">
       <br><br>
       <small>&copyDesenvolvido por Guilherme Benetti Coev</small>
   </footer>
</html>  
