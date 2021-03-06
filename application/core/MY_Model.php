<?php 
    class MY_Model extends CI_Model{

        function __construct(){
            parent::__construct();
        }

        protected function execute_query($sql){
            return $this->db->query($sql);
        }

        protected function get_next_val_serial($table_name, $column_name){
            $sql = "select nextval(pg_get_serial_sequence('$table_name', '$column_name')) as new_id";
            $query = $this->execute_query($sql)->row_array();
            return $query["new_id"];
        }

        protected function get_all($table_name, $condition=""){
            $sql = "select * from $table_name where 1=1 and $condition";
            return $this->execute_query($sql)->result_array();
        }

        protected function get_by_id($table_name, $id_column, $id_value){
            $sql = "select * from $table_name where  $id_column=$id_value ";
            return $this->execute_query($sql)->row_array();
        }

        // protected function insert($table_name, $datas, $column_names=null){
        //     if($column_names != null && count($column_names) != count($datas)){
        //         throw new Exception("Size of column names is different from the datas array");
        //     }
        //     $sql = "";
        //     $value_question_marks = "";
            
        //     for ($i=0; $i < count($datas); $i++) { 
        //         $value_question_marks += ",?";
        //     }
            
        //     if($column_names == null){
                
        //         $sql = "insert into $table_name values (default $value_question_marks ) ";
        //         $this->db->query($sql,$datas);
        //     } else {
        //         $name_question_marks = "";

        //         for ($i=0; $i < count($column_names);$i++){
        //             $name_question_marks += ",?";
        //         }

        //         $sql = "insert into $table_name values ()  "

        //     }
        // }

    }


?>