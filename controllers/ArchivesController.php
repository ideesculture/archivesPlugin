<?php
    /* ----------------------------------------------------------------------
     * simpleListEditor
     * ----------------------------------------------------------------------
     * List & list values editor plugin for Providence - CollectiveAccess
     * Open-source collections management software
     * ----------------------------------------------------------------------
     *
     * Plugin by idéesculture (www.ideesculture.com)
     * This plugin is published under GPL v.3. Please do not remove this header
     * and add your credits thereafter.
     *
     * File modified by :
     * ----------------------------------------------------------------------
     */

    require_once(__CA_MODELS_DIR__.'/ca_lists.php');
    require_once(__CA_MODELS_DIR__.'/ca_list_items.php');
    require_once(__CA_MODELS_DIR__.'/ca_objects.php');
    require_once(__CA_MODELS_DIR__.'/ca_object_labels.php');
    // Sortie PDF : wkhtmltopdf + pdfunite (paquets Debian wkhtmltopdf et poppler-utils), voir Pdf().
    require_once(__CA_APP_DIR__.'/helpers/mediaPluginHelpers.php');
	error_reporting(E_ERROR);

 	class ArchivesController extends ActionController {
 		# -------------------------------------------------------
  		protected $opo_config;		// plugin configuration file
        protected $opa_list_of_lists; // list of lists
        protected $opa_listIdsFromIdno; // list of lists
        protected $opa_locale; // locale id
		private $opo_list;
 		# -------------------------------------------------------
 		# Constructor
 		# -------------------------------------------------------

 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
            parent::__construct($po_request, $po_response, $pa_view_paths);

 			// NO RIGHTS CHECKED FOR NOW
 			/*if (!$this->request->user->canDoAction('can_use_simplelisteditor_plugin')) {
 				$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
 				return;
 			}*/

 			$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/archives/conf/archives.conf');
 			
			$this->opo_list = new ca_lists("object_types");
        }

 		# -------------------------------------------------------
 		# Functions to render views
 		# -------------------------------------------------------
 		public function Index($type="") {
 			$id= $this->request->getParameter("id", pInteger);
			$type_id=$this->opo_config->get("root_type");
			$query = "SELECT ca_object_labels.object_id, idno, name FROM ca_objects left join ca_object_labels ON ca_objects.object_id = ca_object_labels.object_id AND is_preferred=1 AND deleted=0";
			if(!$id) {
				if(!is_array($type_id)) {
					$query .= " WHERE ca_objects.type_id =".$type_id." AND parent_id IS null AND deleted=0";
				} else {
					$query .= " WHERE ca_objects.type_id in (".implode(",",$type_id).") AND parent_id IS null AND deleted=0";
				}
				$this->view->setVar("object_id", false);
			} else {
				$item = new ca_objects($id);
				$this->view->setVar("item", $item);
				$this->view->setVar("object_id", $id);
				$query .= " WHERE parent_id =".$id." and deleted=0";
			}
			$query .= " ORDER BY idno ASC";
			$o_data = new Db();
			$this->view->setVar("qr_result", $o_data->query($query));
 			$this->view->setVar("template", $this->opo_config->get("template"));
            $this->render('index_html.php');
 		}

 		public function Fetch($type="") {

			$id = $this->request->getParameter("id", pInteger);
			$level = $this->request->getParameter("level", pInteger);

			$locale = $this->opo_config->get("locale");
			$query = "SELECT ca_object_labels.object_id, idno, name FROM ca_objects left join ca_object_labels ON ca_objects.object_id = ca_object_labels.object_id AND is_preferred=1 AND deleted=0";
			$type_id=$this->opo_config->get("root_type");


			if(!$id) {
				if(!is_array($type_id)) {
					$query .= " WHERE ca_objects.type_id =".$type_id." AND parent_id IS null AND ca_objects.deleted=0";
				} else {
					$query .= " WHERE ca_objects.type_id in (".implode(",",$type_id).") AND parent_id IS null AND ca_objects.deleted=0";
					//var_dump($query);
					//die();
				}
			} else {
				$query .= " WHERE ca_objects.parent_id =".$id." and deleted=0";
			}

			$locale = $this->opo_config->get("locale");
			if(isset($locale)) {
				$query .= " and ca_object_labels.locale_id = ".$locale;
			}
			$order = $this->opo_config->get("order");
			if(isset($order)) {
				$query .= " ORDER BY ".$order;
			}

			$o_data = new Db();
			$qr_result=$o_data->query($query);
			$result_rows = [];

			while($qr_result->nextRow()) {
				$query2 = "SELECT count(object_id) as numchildren FROM ca_objects WHERE parent_id = ".$qr_result->get('object_id')." AND deleted=0";
				$qr2_result = $o_data->query($query2);
				$qr2_result->nextRow();
				$numchildren=$qr2_result->getRow()["numchildren"];
				$result_rows[] = [
					"object_id"=>$qr_result->get('object_id'),
					"idno"=>$qr_result->get('idno'),
					"numchildren"=>$numchildren
				];
			}
			$this->view->setVar("result_rows", $result_rows);
			$this->view->setVar("level", $level);

			$this->view->setVar("template", $this->ConvertValuesToIdsInsideTemplate($this->opo_config->get("displayTemplate")));
            $this->view->setVar("printLevel", $this->opo_config->get("printLevel"));
            $this->view->setVar("pdfLevel", $this->opo_config->get("pdfLevel"));
            $this->view->setVar("exportLevel", $this->opo_config->get("exportLevel"));
			print $this->render('fetch_html.php');
			exit();
		}

 		public function Export() {
	 		//error_reporting(E_ALL);
			$id= $this->request->getParameter("id", pInteger);
			$this->view->setVar("object_id", $id);
			$item = new ca_objects($id);
			$this->view->setVar("item", $item);
			$this->view->setVar("template", $this->ConvertValuesToIdsInsideTemplate($this->opo_config->get("printTemplate")));
			$result = $this->render('export_html.php');
			print $result;
			exit();
		}

        public function Pdf() {
            //error_reporting(E_ALL);
            $id= $this->request->getParameter("id", pInteger);
            $this->view->setVar("object_id", $id);

            $item = new ca_objects($id);
            $this->view->setVar("item", $item);
            $this->view->setVar("template", $this->ConvertValuesToIdsInsideTemplate($this->opo_config->get("printTemplate")));
            $result = $this->render('pdf_html.php');

            $temp_dir = realpath(__DIR__."/../temp");
            $html_file = $temp_dir."/archives_export_pdf_".$id.".html";
            $export_pdf = $temp_dir."/archives_export_pdf_".$id.".pdf";
            $final_pdf = $temp_dir."/".$id.".pdf";
            // wkhtmltopdf dessine les polices web (Google Fonts) en traces : le PDF n'a alors aucun texte
            // recherchable ni copiable. Pour le PDF seulement, on retire l'@import : la police de repli
            // (sans-serif, DejaVu sur Debian) est integree avec un vrai texte. L'impression HTML n'est pas touchee.
            $result = preg_replace('/@import\s+url\(\s*["\']?https?:\/\/fonts\.googleapis\.com[^)]*\)\s*;?/i', '', $result);
            @unlink($html_file);
            file_put_contents($html_file, $result);

            // Rendu HTML -> PDF par wkhtmltopdf (remplace phantomjs, abandonne et absent des distributions).
            // Chemin lu dans external_applications.conf de providence (wkhtmltopdf_app).
            $wkhtmltopdf = caGetExternalApplicationPath('wkhtmltopdf');
            if (!$wkhtmltopdf || !is_executable($wkhtmltopdf)) { $wkhtmltopdf = '/usr/bin/wkhtmltopdf'; }
            @unlink($export_pdf);
            $command = "timeout 240 ".escapeshellarg($wkhtmltopdf)." --quiet --encoding utf-8 --page-size A4"
                ." --margin-top 15mm --margin-bottom 15mm --margin-left 12mm --margin-right 12mm"
                ." --load-error-handling ignore --load-media-error-handling ignore"
                ." ".escapeshellarg($html_file)." ".escapeshellarg($export_pdf)." 2>&1";
            exec($command, $output, $ret);

            if (!file_exists($export_pdf) || !filesize($export_pdf)) {
                print json_encode(["message" => "Erreur : le PDF n'a pas pu être généré (wkhtmltopdf).", "URL" => null]);
                die();
            }

            // Pièces jointes eventuelles : temp/<id>/<n>_<nom>.pdf, placees avant l'export, dans l'ordre du dossier.
            $files = [];
            $attach_dir = $temp_dir."/".$id;
            if (is_dir($attach_dir)) {
                foreach (scandir($attach_dir) as $file) {
                    if ((strpos($file, "_") > 0) && (substr($file, -4) == ".pdf")) {
                        $parts = explode("_", $file);
                        if (!((int)$parts[0])) { continue; }
                        $files[] = $attach_dir."/".$file;
                    }
                }
            }

            // Ecriture sous un nom temporaire puis renommage : on ne sert jamais un PDF a moitie ecrit.
            $tmp_pdf = $final_pdf.".tmp";
            @unlink($tmp_pdf);
            if (sizeof($files)) {
                // Assemblage par pdfunite (poppler-utils), remplace pdftk.
                $args = array_map('escapeshellarg', array_merge($files, [$export_pdf, $tmp_pdf]));
                exec("timeout 120 pdfunite ".implode(" ", $args)." 2>&1", $output, $ret);
            } else {
                copy($export_pdf, $tmp_pdf);
            }
            if (!file_exists($tmp_pdf) || !filesize($tmp_pdf)) {
                print json_encode(["message" => "Erreur : l'assemblage du PDF a échoué (pdfunite).", "URL" => null]);
                die();
            }
            rename($tmp_pdf, $final_pdf);

			print json_encode(
				[
					"message"=>"Le fichier PDF a été créé",
					"URL"=>"/gestion/app/plugins/archives/temp/".$id.".pdf"
				]
			);

			die();
        }

        private function ConvertValuesToIdsInsideTemplate($template) {
            foreach($template as $key=>$template_per_id) {
            	if($key == "_default") continue;
                //print $key."\n";
                $type_id  = $this->opo_list->getItemIDFromListByItemValue("object_types", $key);
                $template[$type_id] = $template[$key];
                unset($template[$key]);
            }
            return $template;

        }
 	}
 ?>
