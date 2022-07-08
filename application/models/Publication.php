<?php
    class Publication extends MY_Model{

        public function insert($id_publication, $id_client, $id_location,$titre,$description, $prix,$latitude,$longitude,$nbr_piece,$surface){
            $sql="INSERT INTO Publication(id_publication, id_client,id_localisation,titre,description,prix,date_publication,latitude,longitude, nombre_piece,surface) values(%s, %s, %s, %s, %s, %s,NOW(),%s,%s,%s,%s)";
            $sql=sprintf($sql,$this->db->escape($id_publication),$this->db->escape($id_client),$this->db->escape($id_location),$this->db->escape($titre),$this->db->escape($description),$this->db->escape($prix),$this->db->escape($latitude),$this->db->escape($longitude),$this->db->escape($nbr_piece),$this->db->escape($surface));
            $this->db->query($sql);
        }

        public function get_pub($id){
            return $this->get_by_id("v_publication","id_publication",$id);
        }

        public function get_next_pub($limit,$offset){
            $sql="SELECT * from v_publication limit %s offset %s ";
            $sql=sprintf($sql,$this->db->escape($limit),$this->db->escape($offset));
            $query=$this->db->query($sql);
            return $query->result_array();
        }

        public function getPosition($id) {
            return $this
                ->db
                ->query("select longitude lng , latitude lat from publication where id_publication=$id")
                ->row_array();
        }

        public function  get_detail_utilite($id)
		{
			return $this->get_all("v_detail_utilite","id_publication=$id");
		} 

        public function get_id_client($id){
            $data = $this->get_row("publication","id_publication=$id");
            return $data["id_client"];
        }

		public function get_commentaire($id)
		{
			return $this->get_all("v_commentaire","id_publication=$id");
		}

        public function get_limited_commentaire($id,$limit,$offset)
		{
            $sql = "select * from v_commentaire where id_publication=$id limit $limit offset $offset ";
            $query = $this->db->query($sql);
			return $query->result_array() ;
		}


		public function get_detail_tags($id)
		{
			return $this->get_all("v_detail_tags","id_publication=$id");
		}

		public function get_photo($id)
		{
			return $this->get_all("photo","id_publication=$id");
		}

        public function createSearchCondition($titre,$location,$prix_min,$prix_max,$room_min,$room_max,$tags,$tags_util){
            $condition = "";
            if($titre!=""){ //ou isset?
                $condition = $condition." and titre like '%".$titre."%'";
            }
            if($location!=""){
                $condition = $condition." and id_localisation=".$location;
            }
            if($prix_min!=""){
                $condition = $condition." and prix >= ".$prix_min;
            }
            if($prix_max!=""){
                $condition = $condition." and prix <= ".$prix_max;
            }
            if($room_min!=""){
                $condition = $condition." and nombre_piece >= ".$room_min;
            }
            if($room_max!=""){
                $condition = $condition." and nombre_piece <= ".$room_max;
            }
            if($tags != null && count($tags)!=0){
                $condition = $condition." and id_publication in (select id_publication from detail_tags where id_tag = ".$tags[0];
                foreach($tags as $tag){
                    if($tag != $tags[0]){
                        $condition = $condition." or id_tag = ".$tag;
                    }
                }
                $condition = $condition.")";
            }
            if($tags_util != null && count($tags_util)!=0){
                $condition = $condition."and id_publication in (select id_publication from detail_utilite where id_utilite = ".$tags_util[0];
                foreach($$tags_util as $tag){
                    if($tag != $tags_util[0]){
                        $condition = $condition." or id_utilite = ".$tag;
                    }
                }
                $condition = $condition.")";
            }
            return $condition;
        }

        public function search($titre,$location,$prix_min,$prix_max,$room_min,$room_max,$tags,$tags_util){
            $condition=$this->Publication->createSearchCondition($titre,$location,$prix_min,$prix_max,$room_min,$room_max,$tags,$tags_util);
            $sql = "SELECT * FROM v_publication WHERE 1=1".$condition;  
            // echo $sql;
            $query = $this->execute_query($sql);
            return $query->result_array();
        }

        public function simpleSearch($criteria){
            $tag = "id_publication in (select id_publication from v_detail_tags where nom_tag ilike '%$criteria%')";
            $util = "id_publication in (select id_publication from v_detail_utilite where nom_utilite ilike '%$criteria%')";
            $sql = "select * from v_publication where titre ilike '%$criteria%' or $tag or $util ";
            // echo $sql;
            $query = $this->execute_query($sql);

            return $query->result_array();
        }
    }
?>