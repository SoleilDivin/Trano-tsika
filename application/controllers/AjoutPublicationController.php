<?php
    class AjoutPublicationController extends MY_Controller{

        public function uploadImage($dossier,$noms){
            $count=count($noms);
            for($i=0;$i<$count;$i++){
                $_FILES['image']['name'] = $noms[$i];
                $_FILES['image']['type'] = $_FILES['images']['type'][$i];
                $_FILES['image']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                $_FILES['image']['error'] = $_FILES['images']['error'][$i];
                $_FILES['image']['size'] = $_FILES['images']['size'][$i];

                $config['upload_path'] = $dossier; 
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = '5000';
                $config['file_name'] = $_FILES['images']['name'][$i];
                $this->load->library('upload',$config);      
                $this->upload->do_upload('image');
            }
        }

        public function set_rules(){
            $this->form_validation->set_rules('titre','Titre','required');
            $this->form_validation->set_rules('location','Location','required');
            $this->form_validation->set_rules('room','Rooms','required');
            $this->form_validation->set_rules('description','Description','required');
            $this->form_validation->set_rules('tags','Tags','required');
            $this->form_validation->set_rules('lat','lat','required');
            $this->form_validation->set_rules('lng','lng','required');
            $this->form_validation->set_rules('tagsUtil','Tags_Util','required');
            $this->form_validation->set_rules('price','price','required');
            // $this->form_validation->set_rules('surface','Surface','required');
        }

        public function ajout_publication(){

            $this->load->model('Publication');
            $this->load->model('Photo');
            $this->load->model('DetailUtilite');
            $this->load->model('DetailTag');

            $inputs = ["titre","location","room","description","tags","images","lat","lng","price","surface"];
            //autoload session + form_validation
            $datas = $this->get_datas($inputs,"post");
            $tagsUtil=$this->input->post('tagsUtil');
            $tags=$this->input->post('tags');
            $this->set_rules();

            $count = count($_FILES['images']['name']);
            
            if ($this->form_validation->run()){
                $this->db->trans_start();
                $noms=array();
                $id_pub=$this->Publication->get_next_val_serial("Publication","id_publication");
                
                $this->Publication->insert($id_pub,$_SESSION["id_client"],$datas["location"],$datas["titre"],$datas["description"],$datas["price"],
                $datas["lieu"],$datas["lat"],$datas["lng"],$datas["room"],$datas["surface"]);
                
                $dossier="assets/img/pub_".$id_pub."/";
                //insertion des photos dans la bdd
                for($i=0;$i<$count;$i++){
                    $id_img=$this->Photo->get_next_val_serial("Photo","id_photo");
                    $lien=base_url($dossier.$id_img.$_FILES['images']['type'][$i]);
                    $this->Photo->insert($id_img,$lien,$id_pub);
                    $noms[]=$id_img.$_FILES['images']['type'][$i];
                }

                //insertion detail tag
                foreach ($tags as $tag) {
                    $this->DetailTag->insert($tag,$id_pub);
                }

                //insertion detail util
                foreach ($tagsUtil as $util) {
                    $this->DetailUtilite->insert($util,$id_pub);
                }
                $this->db->trans_complete();

                mkdir($dossier);
                $this->uploadImage($dossier,$noms);
                http_response_code(200);
            }else{
                http_response_code(400);
            }
        } 
    }
?>