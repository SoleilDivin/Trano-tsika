<?php
    class DetailTag extends MY_Model{
        public function insert($id_tag, $id_publication){
            $sql="INSERT INTO Detail_tags(id_detail_tag,id_tag,id_publication) VALUES (default,%s,%s)";
            $sql=sprintf($sql,$this->db->escape($id_tag),$this->db->escape($id_publication));
            $this->db->query($sql);
        }
    }
?>