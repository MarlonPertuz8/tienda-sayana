<?php
    class Mysql extends Conexion {
        private $conexion;
        private $strquery;
        private $arrValues;
    
        public function __construct(){
			parent::__construct();
			$this->conexion = $this->connect();
            }

        public function insert(string $query, array $arrValues)
        {
            $this->strquery = $query;
            $this->arrValues = $arrValues;
            $insert = $this->conexion->prepare($this->strquery);
            $resInsert = $insert->execute($this->arrValues);
            if($resInsert){
                $lastInsert = $this->conexion->lastInsertId();
            }else{
                $lastInsert = 0;
            }
            return $lastInsert;
        }
        // Buscar un registro
        public function select(string $query, array $arrValues = [])
        {
            $this->strquery = $query;
            $this->arrValues = $arrValues;

            $result = $this->conexion->prepare($this->strquery);
            $result->execute($this->arrValues);
            $data = $result->fetch(PDO::FETCH_ASSOC);

            return $data;
}
        
        //devolver varios registros
        public function select_all(string $query, array $arrValues = [])
        {
            $this->strquery = $query;
            $this->arrValues = $arrValues;

            $result = $this->conexion->prepare($this->strquery);
            $result->execute($this->arrValues);
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        }
        //Actualizar registros
        public function update(string $query, array $arrValues)
        {
            $this->strquery = $query;
            $this->arrValues = $arrValues;
            $update = $this->conexion->prepare($this->strquery);
            $resExecute = $update->execute($this->arrValues);
            return $resExecute;
        }
        //Eliminar registros
       public function delete(string $query)
        {
            $this->strquery = $query;
            $result = $this->conexion->prepare($this->strquery);
            $del = $result->execute();
            return $del;

    }

}
?>