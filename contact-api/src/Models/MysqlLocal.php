<?php

namespace src\Models;

use Exception;

class MysqlLocal
{
    private $ms_server, $ms_login, $ms_senha, $ms_db;
    public $mysqry, $rows, $ulid, $resul, $connecting, $mysqryPag, $mysvazio, $inicio, $fim, $myspaginacao, $myspaginacao2;

    function __construct()
    {
        $ms_server = "localhost";
        $ms_login = "root";
        $ms_senha = "";
        $ms_db = 'contacts_api';

        $this->connecting = mysqli_connect($ms_server, $ms_login, $ms_senha, $ms_db);
        if (!$this->connecting) {
            throw new Exception("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->connecting, 'utf8');
    }
    function colunas($ms_table)
    {
        $columns = "SHOW COLUMNS FROM $ms_table";
        $this->mysqry = mysqli_query($this->connecting, $columns) or die(mysqli_error($this->connecting));
    }

    function tables()
    {
        $columns = "SHOW TABLES";
        $this->mysqry = mysqli_query($this->connecting, $columns) or die(mysqli_error($this->connecting));
    }

    function estruturas($ms_table)
    {
        $columns = "SHOW CREATE TABLE $ms_table";
        $this->mysqry = mysqli_query($this->connecting, $columns) or die(mysqli_error($this->connecting));
    }

    function del($ms_table, $ms_arg)
    {
        $deleting = "DELETE from $ms_table $ms_arg";
        $qry = mysqli_query($this->connecting, $deleting) or die(mysqli_error($this->connecting));
        $this->rows = mysqli_affected_rows($this->connecting);
    }

    function update($ms_table, $ms_fields, $ms_arg)
    {
        $updating = "UPDATE $ms_table set $ms_fields $ms_arg";
        $qry = mysqli_query($this->connecting, $updating) or die(mysqli_error($this->connecting));
        if (mysqli_affected_rows($this->connecting) < 0) $this->resul = "erro";
        else $this->resul = "ok";
    }

    function add($ms_table, $ms_values)
    {
        $inserting = "INSERT INTO $ms_table SET $ms_values";
        $qry = mysqli_query($this->connecting, $inserting) or die(mysqli_error($this->connecting));
        if (mysqli_affected_rows($this->connecting) == 0) $this->resul = "erro";
        $this->ulid = mysqli_insert_id($this->connecting);
    }

    function consult($ms_table, $ms_fields, $ms_arg)
    {
        $sql = "SELECT $ms_fields from $ms_table $ms_arg";
        $this->mysqry = mysqli_query($this->connecting, $sql) or die(mysqli_error($this->connecting));
        $this->rows = mysqli_num_rows($this->mysqry);
    }

    function exists($ms_table)
    {
        $sql = "SELECT * from $ms_table";
        $this->mysqry = mysqli_query($this->connecting, $sql);
    }

    function execute($sql)
    {
        $this->mysqry = mysqli_query($this->connecting, $sql) or die(mysqli_error($this->connecting));
        $this->rows = mysqli_num_rows($this->mysqry);
    }

    // Novo método para fechar a conexão
    function closeConnection()
    {
        if ($this->connecting) {
            mysqli_close($this->connecting);
            $this->connecting = null; // Definir como nulo para evitar fechamentos múltiplos
        }
    }

    // Destrutor para garantir que a conexão seja fechada
    function __destruct()
    {
        $this->closeConnection();
    }
}
