<?php

  require_once 'conexion.php';
  include '../Clases/Bombero.php';
  include_once "ListasDAO.php";


  class BomberoDAO{

    function __construct(){}

  //Realiza el ingreso del bombero a la base de datos
  function ingresarBombero($bombero){
    $rut = $bombero->__get("run");

    if(!$this->siExiste($rut)){
                          $consulta = "insert into tbl_bombero(nombre,apellido,run,fecha_nac,domicilio,telefono,id_compania)
                               values('".$bombero->__get("nombre")."','".$bombero->__get("apellido")."','".$bombero->__get("run")."',
                                  '".$bombero->__get("fechaNac")."','".$bombero->__get("domicilio")."','".$bombero->__get("telefono")."',
                                  '".$bombero->__get("idComp")."' )
                                 ";
                                 $db = conectar();   //realiza la Conexion
                                   $stm = $db->prepare($consulta); //Prepara la $consulta
                                   if($stm->execute()){
                                               return 1;
                                   }else{
                                      return 0;
                                   }
          }
  }

  //Obtiene el ID del bombero
  function getID($run){
    $db = conectar();

    try {
      $stmt = $db->prepare("SELECT id_bombero FROM tbl_bombero where run =  '".$run."' " );
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($rows as $row) {
        $id = $row["id_bombero"];
        return $id;
      }

    } catch (PDOException $e) {
        echo "Error ".$e;
      }
      return 0;

  }

    //Verifica que el bombero ya está ingresado en el sistema o no
    function siExiste($run){
        $db = conectar();
        try {
          $stmt = $db->prepare("SELECT * FROM tbl_bombero WHERE run = '".$run."' ");
        	$stmt->execute();
        	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

          //verificar  echo $stmt->rowCount();
        } catch(PDOException $ex) {
            echo "Ocurrió un error<br>";
            echo $ex->getMessage();
            exit;
        }
        if($stmt->rowCount() > 0){
          return true;
        }else{
          return false;
        }
      }

    function modificarBombero($bombero){
      $run = $bombero ->__get("run");
      $nombre =$bombero->__get("nombre");
      $apellido = $bombero->__get("apellido");
      $fechaNac = $bombero->__get("fechaNac");
      $domicilio =$bombero->__get("domicilio");
      $telefono = $bombero->__get("telefono");
      $id_compania =$bombero->__get("idComp");
      $db = conectar();
      try{
      $consulta = "
            UPDATE tbl_bombero b1  set b1.nombre = '".$nombre."', b1.apellido='".$apellido."', b1.fecha_nac ='".$fechaNac."', b1.domicilio = '".$domicilio."', b1.telefono = '".$telefono."', b1.id_compania = '".$id_compania."'
            where b1.run = '".$run."'
            ";
            $stm = $db->prepare($consulta); //Prepara la $consulta
            if($stm->execute()){
                        return 1;
            }else{
               return 0;
            }
          }catch(PDOException $e){
              echo   ' <script languaje="javascript">alertaModifcarError();</script>  ' ;

          }


      }

    //Lista a los bomberos por compañia
    function listarBomberos($compania){
      $db = conectar();
      try {
        $stmt = $db->prepare("
        select bombero.run as Rut,bombero.id_bombero as IDBombero, bombero.nombre as NombreB, bombero.apellido as ApellidoB, info.fotografia as foto,
        especialidad.nombre as Especialidad, info.maquinista as Maquinista, curso.tipo_curso as Curso
         from
        tbl_bombero bombero inner join tbl_info_personal info on bombero.id_bombero = info.id_bombero
        inner join tbl_compania comp on bombero.id_compania = comp.id_compania
        inner join tbl_especialidad especialidad on info.id_especialidad = especialidad.id_especialidad
        inner join tbl_cursos curso on info.id_cursos = curso.id_cursos
        where comp.id_compania = ".$compania."
        " );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
          $id = $row["IDBombero"];
          $rut = $row["Rut"];
          $nombres = $row["NombreB"];
          $nombre = explode(" ",$nombres);
          $apellidos = $row["ApellidoB"];
          $apellido = explode(" ",$apellidos);
          $especialidad = $row["Especialidad"];
          $maquinista = $row["Maquinista"];
          $foto = $row["foto"];
          $curso = $row["Curso"];
          echo "<table class='tablaUsuarioss' >
                <tr>
                  <td> <img class='fotoBombero' src='".$foto."'></td>
                </tr>

                <tr>
                  <td>".$nombre[0]." ".$apellido[0]."</td>
                </tr>

                <tr>
                  <td>".$curso." </td>
                </tr>
                <tr>
                  <td>Tipo: ".$especialidad."</td>
                </tr>
                <tr>
                  <td>Maquinista: ".$maquinista."</td>
                </tr>
              </table>";

        }

      } catch (PDOException $e) {
          echo "Error ".$e;
        }

    }

    function getBombero($rut){
      $db = conectar();
      $listas = new ListasDAO();

      try {
        $stmt = $db->prepare("
        select b.nombre as Nombre, b.apellido as Apellido, b.fecha_nac FN, b.domicilio as Domicilio, b.telefono as Telefono, b.id_compania as IC,
        ip.familiar_contacto as FC, ip.tel_contacto as TC, ip.maquinista as Maquinista, ip.fotografia as Foto, ip.id_especialidad as Especialidad, ip.id_cargo as Cargo, ip.id_cursos as Cursos, ip.id_salud as Salud, ip.id_grupo_sanguineo as GS
        from tbl_bombero b inner join tbl_info_personal ip on b.id_bombero = ip.id_bombero
        where b.run = '".$rut."'
        " );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
          $nombres = $row["Nombre"];
          $apellidos = $row["Apellido"];
          $fechaNac= $row["FN"];
          $domicilio = $row["Domicilio"];
          $telefono =$row["Telefono"];
          $prevision = $row["Salud"];
          $grupoS = $row["GS"];
          $familiarContacto = $row["FC"];
          $telefonoContacto = $row["TC"];
          $compañia = $row["IC"];
          $cargo = $row["Cargo"];
          $especialidad =$row["Especialidad"];
          $curso = $row["Curso"];
          $maquinista =$row["Maquinista"];
          $fotografia = $row["Foto"];

          $array= array(
            "nombres" => $nombres,
            "apellidos" => $apellidos,
            "fechaNac" => $fechaNac,
            "domicilio" => $domicilio,
            "telefono" => $telefono,
            "prevision" => $prevision,
            "grupoS" => $grupoS,
            "familiarContacto" => $familiarContacto,
            "telefonoContacto" => $telefonoContacto,
            "compania" => $compañia,
            "cargo" => $cargo,
            "especialidad" => $especialidad,
            "curso" => $curso,
            "maquinista" => $maquinista,
            "fotografia" => $fotografia
          );
          return $array;
        }
      } catch (PDOException $e) {
          echo "Error ".$e;
        }
    }

    //genera una lista de los bomberos, donde con su rut se podrá ingresar a su modificación
    function listaBomberosCompania($compania){
      $db = conectar();

      try {
        $stmt = $db->prepare("select bombero.run as Rut, bombero.nombre as Nombre, bombero.apellido as Apellido from tbl_bombero bombero where bombero.id_compania=".$compania." LIMIT 30  " );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
          $rut = $row["Rut"];
          $nombres = $row["Nombre"];
          $apellidos = $row["Apellido"];
          $nombre = explode(" ",$nombres);
          $apellido = explode(" ",$apellidos);
          echo  '<a href="BomberoInfo.php?RUN='.$rut.'"> '.$nombre["0"]." ".$apellido["0"].'</a><br>';
        }

      } catch (PDOException $e) {
          echo "Error ".$e;
        }

    }

    //obtiene la cantidad de bomberos
    function totalBomberosCompania($compania){
      $db = conectar();
      try {
        $stmt = $db->prepare("select count(b.id_bombero) as Cantidad from
tbl_registro_bombero rb INNER JOIN tbl_bombero B on rb.id_bombero = b.id_bombero
inner JOIN tbl_compania C on b.id_compania = C.id_compania
where b.id_compania = '".$compania."' and rb.estado = 1" );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows["0"]["Cantidad"];

      } catch (PDOException $e) {
          echo "Error ".$e;
        }

    }

    function totalBomberos(){
      $db = conectar();
      try {
        $stmt = $db->prepare("select count(b1.id_bombero) as Cantidad from tbl_bombero B1 " );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows["0"]["Cantidad"];

      } catch (PDOException $e) {
          echo "Error ".$e;
        }

    }

    //Lista los participantes de cada compañia con limite de 6(puede ser modificado)
    function listaCompania($compania){
      $db = conectar();

      try {
        $stmt = $db->prepare("select bombero.nombre as Nombre, bombero.apellido as Apellido from tbl_bombero bombero where bombero.id_compania=".$compania." LIMIT 30  " );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
          $nombres = $row["Nombre"];
          $apellidos = $row["Apellido"];
          $nombre = explode(" ",$nombres);
          $apellido = explode(" ",$apellidos);
          echo $nombre["0"]." ".$apellido["0"]."<br>";
        }

      } catch (PDOException $e) {
          echo "Error ".$e;
        }

    }

    function bomberosActivos($compania){
      $db = conectar();
      try {
        $stmt = $db->prepare("
        select bombero.run as Rut,bombero.id_bombero as IDBombero, bombero.nombre as NombreB, bombero.apellido as ApellidoB, info.fotografia as foto,
        especialidad.nombre as Especialidad, info.maquinista as Maquinista, curso.tipo_curso as Curso
         from
        tbl_bombero bombero
        inner join tbl_registro_bombero rb on bombero.id_bombero = rb.id_bombero
        inner join tbl_info_personal info on bombero.id_bombero = info.id_bombero
        inner join tbl_compania comp on bombero.id_compania = comp.id_compania
        inner join tbl_especialidad especialidad on info.id_especialidad = especialidad.id_especialidad
        inner join tbl_cursos curso on info.id_cursos = curso.id_cursos

        where comp.id_compania = ".$compania." and rb.estado = '1'
        " );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
          $id = $row["IDBombero"];
          $rut = $row["Rut"];
          $nombres = $row["NombreB"];
          $nombre = explode(" ",$nombres);
          $apellidos = $row["ApellidoB"];
          $apellido = explode(" ",$apellidos);
          $especialidad = $row["Especialidad"];
          $maquinista = $row["Maquinista"];
          $foto = $row["foto"];
          $curso = $row["Curso"];
          echo "<table class='tablaUsuarioss' >
                <tr>
                  <td> <img class='fotoBombero' src='".$foto."'></td>
                </tr>

                <tr>
                  <td>".$nombre[0]." ".$apellido[0]."</td>
                </tr>

                <tr>
                  <td>".$curso." </td>
                </tr>
                <tr>
                  <td>Tipo: ".$especialidad."</td>
                </tr>
                <tr>
                  <td>Maquinista: ".$maquinista."</td>
                </tr>
              </table>";

        }

      } catch (PDOException $e) {
          echo "Error ".$e;
        }

    }
}

  //
  // $teset = new BomberoDAO();
  //
  // $bombero = new Bombero("Carlos Raúl","Ramos Peñaloza","19923124-1", "1998-02-21","Meson del Nelson", "856452251",2);
  //
  //
  //
  // echo "<br>PRUEBA FINAL<br>";
  //
  //
  // echo $teset->getID("19864118-9");







 ?>
