<?php
	
	namespace Product\Controller;
	
	use Zend\Mvc\Controller\AbstractActionController;
	use Zend\View\Model\ViewModel;
	use Product\Model\Utility;
	use Product\Model\Resize;
	use Product\Model\Product;
	use Product\Model\Image;
	use Zend\Session\Container;
	use Zend\Db\Sql\Select;
	use Zend\Paginator\Paginator;
	use Zend\Paginator\Adapter\Iterator as paginatorIterator;
	
	class IndexController extends AbstractActionController {
		
		protected $Product;
		
		public function getProductTable() {
			if (!$this->Product) {
				$pst = $this->getServiceLocator();
				$this->Product = $pst->get('Product\Model\ProductTable');
			}
			return $this->Product;
		}
		
		protected $Category;
		
		public function getProductcategoryTable() {
			if (!$this->Category) {
				$pst = $this->getServiceLocator();
				$this->Category = $pst->get('Product\Model\ProductTablecategory');
			}
			return $this->Category;
		}
		
		protected $Manufa;
		
		public function getProductmanufaTable() {
			if (!$this->Manufa) {
				$pst = $this->getServiceLocator();
				$this->Manufa = $pst->get('Product\Model\ProductTablemanufacture');
			}
			return $this->Manufa;
		}
		
		protected $Image;
		
		public function getImageTable() {
			if (!$this->Image) {
				$pst = $this->getServiceLocator();
				$this->Image = $pst->get('Product\Model\ImageTable');
			}
			return $this->Image;
		}
		
		protected $Chatlieu;
		
		public function getChatlieuTable() {
			if (!$this->Chatlieu) {
				$pst = $this->getServiceLocator();
				$this->Chatlieu = $pst->get('Product\Model\ChatlieuTable');
			}
			return $this->Chatlieu;
		}
		
		protected $Xuatxu;
		
		public function getXuatxuTable() {
			if (!$this->Xuatxu) {
				$pst = $this->getServiceLocator();
				$this->Xuatxu = $pst->get('Product\Model\XuatxuTable');
			}
			return $this->Xuatxu;
		}
		
		public function indexAction() {
			
			$this->layout('layout/user.phtml');
			/*$select = new Select();
				$page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
				$data_product = $this->getProductTable()->listproduct_all();
				
				$itemsPerPage = 10;
				$data_product->current();
				$paginator = new Paginator(new paginatorIterator($data_product));
				$paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(5);
				
				//print_r($paginator);die;
				return new ViewModel(array(
				'page' => $page,
				'paginator' => $paginator,
			));*/
			$data = $this->getProductTable()->listproduct();   
			foreach ($data as $key=>$value){
				$id_parent = $value['parent_id'];
				$parent[$id_parent] = $this->getProductcategoryTable()->getparent_name($id_parent);
				
			}
			
			//Log File
			$witelog = new Utility();
			$text = 'Xem danh s??ch s???n ph???m';
			$witelog->witelog($text);
			//--------------------------
			return array('data' => $data, "parent_name"=>$parent);
		}
		
		// Xem chi ti???t s???n ph???m
		public function viewAction() {
			$this->layout('layout/user.phtml');
			$id = $this->params()->fromRoute('id');
			$data = $this->getProductTable()->productdetail($id);
			// print_r($data);die;      
			$data_img = $this->getImageTable()->listimg($id);
			
			return array('data' => $data, 'data_img' => @$data_img);
		}
		
		public function addAction() {
			$Uty = new Utility();
			$this->layout('layout/user.phtml');
			
			$session_user = new Container('user');
			$id_user = $session_user->idus;
			
			$data_cat = $this->getProductcategoryTable()->listcatparent();
			$data_manu = $this->getProductmanufaTable()->load_select();
			$data_chatlieu = $this->getChatlieuTable()->listchatlieu();
			$data_xuatxu = $this->getXuatxuTable()->listxuatxu();
			
			foreach ($data_cat as $key=>$value){
				$id_cat = $value['id'];
				$data_parent[$id_cat]=  $this->getProductcategoryTable()->load_parent_admin($id_cat);
			}
			
			if ($this->request->isPost()) {
				$name = addslashes(trim($this->params()->fromPost('name')));
				$code_id = addslashes(trim($this->params()->fromPost('code_id')));
				$cat_id = addslashes(trim($this->params()->fromPost('cat_id')));
				$manufa_id = addslashes(trim($this->params()->fromPost('manufa_id')));
				$quantity = $Uty->removescript(addslashes(trim($this->params()->fromPost('quantity'))));
				$status_pro = addslashes(trim($this->params()->fromPost('status_pro')));
				$price = $Uty->removescript(addslashes(trim($this->params()->fromPost('price'))));
				$product_dv = addslashes(trim($this->params()->fromPost('product_dv')));
				$sales = addslashes(trim($this->params()->fromPost('sales')));
				$price_sales = addslashes(trim($this->params()->fromPost('price_sales')));
				$status = addslashes(trim($this->params()->fromPost('status')));
				$pro_featured = addslashes(trim($this->params()->fromPost('pro_featured')));
				//$pro_new = addslashes(trim($this->params()->fromPost('pro_new')));
				$description = addslashes(trim($this->params()->fromPost('description')));
				$content = addslashes(trim($this->params()->fromPost('content')));
				$xuatxu = addslashes(trim($this->params()->fromPost('xuatxu')));
				$chatlieu = addslashes(trim($this->params()->fromPost('chatlieu')));
				$show_index = addslashes(trim($this->params()->fromPost('show_index')));
				$seo_title = $Uty->removescript(addslashes(trim($this->params()->fromPost('seo_title'))));
				$seo_keyword = $Uty->removescript(addslashes(trim($this->params()->fromPost('seo_keyword'))));
				$seo_description = $Uty->removescript(addslashes(trim($this->params()->fromPost('seo_description'))));
				$alias = strtolower($Uty->chuyenDoi($name)) . '-' . time();
				
				$date = date("Y-m-d H:i:s");
				
				$dirpath = str_replace("\\", "/", WEB_MEDIA . "/media/images/imgProduct");
				$listNameImg = $_FILES["image"]["name"];
				$numimg = count($listNameImg);
				$array_temp_name = $_FILES['image']['tmp_name'];
				if ($listNameImg[0] == null) {
					$alert = '<p class="bg-warning">B???n ph???i ch???n h??nh ???nh cho s???n ph???m</p>';
					return array(
                    'data_cat' => $data_cat,
                    'data_parent'=>$data_parent,
                    'alert' => $alert,
                    'data_manu' => $data_manu,
                    'data_xuatxu' => $data_xuatxu,
                    'data_chatlieu' => $data_chatlieu,
					);
				}
				
				//check kho???ng gi??
				if($sales==1){
					$price_khoanggia = $price - ($price * $price_sales / 100);
					}else{
					$price_khoanggia=$price;
				}
				if ($price_khoanggia <= 200000) {
					$khoanggia = 1;
					} elseif ($price_khoanggia > 200000 && $price_khoanggia <= 500000) {
					$khoanggia = 2;
					} elseif ($price_khoanggia > 500000 && $price_khoanggia <= 700000) {
					$khoanggia = 3;
					} elseif ($price_khoanggia > 700000 && $price_khoanggia <= 1000000) {
					$khoanggia = 4;
					} elseif ($price_khoanggia > 1000000 && $price_khoanggia <= 3000000) {
					$khoanggia = 5;
					} elseif ($price_khoanggia > 3000000 && $price_khoanggia <= 5000000) {
					$khoanggia = 6;
					} elseif ($price_khoanggia > 5000000 && $price_khoanggia <= 10000000) {
					$khoanggia = 7;
					} elseif ($price_khoanggia > 10000000) {
					$khoanggia = 8;
				}
				
				//end check kho???ng gi??
				$checkname = $this->getProductTable()->checkname($code_id);
				if ($checkname) {
					$data_product = array(
                    'code_id' => $code_id,
                    'product_name' => $name,
                    'alias' => $alias,
                    'cat_id' => $cat_id,
                    'manufa_id' => $manufa_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'status_pro' => $status_pro,
                    'product_dv' => $product_dv,
                    'sales' => $sales,
                    'price_sales' => $price_sales,
                    'status' => $status,
                    'product_featured' => $pro_featured,
                    //'product_new' => $pro_new,
                    'description' => stripslashes($description),
                    'content' => stripslashes($content),
                    'xuatxu' => $xuatxu,
                    'chatlieu' => $chatlieu,
                    'khoanggia' => $khoanggia,
                    'seo_title' => stripslashes($seo_title),
                    'seo_keyword' => stripslashes($seo_keyword),
                    'seo_description' => stripslashes($seo_description),
                    'date' => $date,
                    'date_mod' => $date,
                    'user_post' => $id_user,
                    'user_edit' => '',
                    'show_index' => $show_index,
					);
					// print_r($data_product);
					//die;
					$obj_pro = new Product();
					$obj_pro->exchangeArray($data_product);
					$this->getProductTable()->addproduct($obj_pro);
					
					$product_new = $this->getProductTable()->getproduct_new();
					$idproduct_new = $product_new['id'];
					
					//Upload nhi???u ???nh app-------------------------------------------------------          
					for ($i = 0; $i < $numimg; $i++) {
						$ext = substr(strrchr($listNameImg[$i], '.'), 1);
						$filename = time() . $i . "." . $ext;
						/*if (move_uploaded_file($array_temp_name[$i], "$dirpath/images/$filename")) {
							$resizeObj = new Resize("$dirpath/images/$filename");
							$resizeObj->resizeImage(170, 208, 'crop');
							$resizeObj->saveImage($dirpath . '/thumb/' . $filename, 100);
							
							$resizeObj->resizeImage(398, 398, 'crop');
							$resizeObj->saveImage($dirpath . '/medium/' . $filename, 100);
						}*/
						copy($array_temp_name[$i], $dirpath . '/thumb/' . $filename);
						$Uty->load($array_temp_name[$i]);					
						$Uty->resizeToWidth(171);
						$Uty->save($dirpath . '/thumb/' . $filename); // ???nh thumb
						
						copy($array_temp_name[$i], $dirpath . '/medium/' . $filename);
						$Uty->load($array_temp_name[$i]);					
						$Uty->resizeToWidth(398);
						$Uty->save($dirpath . '/medium/' . $filename); // ???nh medium
						
						move_uploaded_file($array_temp_name[$i], $dirpath.'/images/'.$filename); //anhr g???c
						
						
						if ($i == 0) {
							$default = 1;
							} else {
							$default = 0;
						}
						// L??u th??ng tin ???nh v??o db
						$data_img = array(
                        'id_product' => $idproduct_new,
                        'img' => 'imgProduct/images/' . $filename,
                        'medium' => 'imgProduct/medium/' . $filename,
                        'thumbnail' => 'imgProduct/thumb/' . $filename,
                        'status' => $default
						);
						$obj_img = new Image();
						$obj_img->exchangeArray($data_img);
						$this->getImageTable()->addimg($obj_img);
						}//End upload Img               
						
						$data_cat = $this->getProductcategoryTable()->listcatparent();
						$alert = '<p class="bg-success">Th??m s???n ph???m th??nh c??ng</p>';
						
						//Log File
						$witelog = new Utility();
						$text = 'Th??m s???n ph???m m???i c?? m?? l?? '.$code_id;
						$witelog->witelog($text);
						//--------------------------
						
						return array(
						'data_cat' => $data_cat,
						'data_parent'=>$data_parent,
						'alert' => $alert,
						'data_manu' => $data_manu,
						'data_xuatxu' => $data_xuatxu,
						'data_chatlieu' => $data_chatlieu,
						);
						} else {
						$alert = '<p class="bg-warning">M?? s???n ph???m n??y ???? t???n t???i kh??ng th??? th??m ???????c</p>';
						return array(
						'data_cat' => $data_cat,
						'data_parent'=>$data_parent,
						'alert' => $alert,
						'data_manu' => $data_manu,
						'data_xuatxu' => $data_xuatxu,
						'data_chatlieu' => $data_chatlieu,
						);
				}
			}
			return array(
            'data_cat' => $data_cat,
            'data_parent'=>$data_parent,
            'data_manu' => $data_manu,
            'data_xuatxu' => $data_xuatxu,
            'data_chatlieu' => $data_chatlieu,
			);
		}
		
		public function editAction() {		
			//echo date_default_timezone_get();
			$Uty = new Utility();
			
			$session_user = new Container('user');
			$id_user = $session_user->idus;
			$fullname = $session_user->fullname;
			
			$this->layout('layout/user.phtml');
			$id = addslashes(trim($this->params()->fromRoute('id', 0)));
			$data_detail = $this->getProductTable()->productdetail($id);        
			$data_manu = $this->getProductmanufaTable()->load_select();
			$data_img[$id] = $this->getImageTable()->listimg($id);
			$data_chatlieu = $this->getChatlieuTable()->listchatlieu();
			$data_xuatxu = $this->getXuatxuTable()->listxuatxu();
			
			$data_cat = $this->getProductcategoryTable()->listcategory();
			foreach ($data_cat as $key=>$value){
				$id_cat = $value['id'];
				$data_parent[$id_cat]=  $this->getProductcategoryTable()->load_parent_admin($id_cat);
			}
			
			if ($this->request->isPost()) {
				$name = addslashes(trim($this->params()->fromPost('name')));
				$code_id = addslashes(trim($this->params()->fromPost('code_id')));
				$cat_id = addslashes(trim($this->params()->fromPost('cat_id')));
				$manufa_id = addslashes(trim($this->params()->fromPost('manufa_id')));
				$quantity = $Uty->removescript(addslashes(trim($this->params()->fromPost('quantity'))));
				$status_pro = addslashes(trim($this->params()->fromPost('status_pro')));
				$price = $Uty->removescript(addslashes(trim($this->params()->fromPost('price'))));
				$product_dv = addslashes(trim($this->params()->fromPost('product_dv')));
				$sales = addslashes(trim($this->params()->fromPost('sales')));
				$price_sales = addslashes(trim($this->params()->fromPost('price_sales')));
				$status = addslashes(trim($this->params()->fromPost('status')));
				$pro_featured = addslashes(trim($this->params()->fromPost('pro_featured')));
				//$pro_new = addslashes(trim($this->params()->fromPost('pro_new')));
				$description = addslashes(trim($this->params()->fromPost('description')));
				$content = addslashes(trim($this->params()->fromPost('content')));
				$xuatxu = addslashes(trim($this->params()->fromPost('xuatxu')));
				$chatlieu = addslashes(trim($this->params()->fromPost('chatlieu')));
				$show_index = addslashes(trim($this->params()->fromPost('show_index')));
				$seo_title = $Uty->removescript(addslashes(trim($this->params()->fromPost('seo_title'))));
				$seo_keyword = $Uty->removescript(addslashes(trim($this->params()->fromPost('seo_keyword'))));
				$seo_description = $Uty->removescript(addslashes(trim($this->params()->fromPost('seo_description'))));
				
				$date = date("Y-m-d H:i:s");
				if ($name == $data_detail['product_name']) {
					$alias = $data_detail['alias'];
					} else {
					$alias = strtolower($Uty->chuyenDoi($name)) . '-' . time();
				}
				//check kho???ng gi??
				if($sales==1){
					$price_khoanggia = $price - ($price * $price_sales / 100);
					}else{
					$price_khoanggia=$price;
				}
				if ($price_khoanggia <= 200000) {
					$khoanggia = 1;
					} elseif ($price_khoanggia > 200000 && $price_khoanggia <= 500000) {
					$khoanggia = 2;
					} elseif ($price_khoanggia > 500000 && $price_khoanggia <= 700000) {
					$khoanggia = 3;
					} elseif ($price_khoanggia > 700000 && $price_khoanggia <= 1000000) {
					$khoanggia = 4;
					} elseif ($price_khoanggia > 1000000 && $price_khoanggia <= 3000000) {
					$khoanggia = 5;
					} elseif ($price_khoanggia > 3000000 && $price_khoanggia <= 5000000) {
					$khoanggia = 6;
					} elseif ($price_khoanggia > 5000000 && $price_khoanggia <= 10000000) {
					$khoanggia = 7;
					} elseif ($price_khoanggia > 10000000) {
					$khoanggia = 8;
				}
				
				//end check kho???ng gi??
				$data_product = array(
                'code_id' => $code_id,
                'product_name' => $name,
                'alias' => $alias,
                'cat_id' => $cat_id,
                'manufa_id' => $manufa_id,
                'quantity' => $quantity,
                'price' => $price,
                'status_pro' => $status_pro,
                'product_dv' => $product_dv,
                'sales' => $sales,
                'price_sales' => $price_sales,
                'status' => $status,
                'product_featured' => $pro_featured,
                //'product_new' => $pro_new,
                'description' => stripslashes($description),
                'content' => stripslashes($content),
                'xuatxu' => $xuatxu,
                'chatlieu' => $chatlieu,
                'khoanggia' => $khoanggia,
                'seo_title' => stripslashes($seo_title),
                'seo_keyword' => stripslashes($seo_keyword),
                'seo_description' => stripslashes($seo_description),
                'date' => $data_detail['date'],
                'date_mod' => $date,
                'user_post' => $data_detail['user_post'],
                'user_edit' => $fullname,
                'show_index' => $show_index,
				);
				//print_r($data_product);die;
				$checkname = $this->getProductTable()->checkname($code_id);
				if ($code_id == $data_detail['code_id']) {
					$obj_pro = new Product();
					$obj_pro->exchangeArray($data_product);
					$this->getProductTable()->updateproduct($id, $obj_pro);
					$alert = '<p class="bg-success">S???a s???n ph???m th??nh c??ng</p>';
					
					//Log File
					$witelog = new Utility();
					$text = 'S???a s???n ph???m c?? m?? l?? '.$code_id;
					$witelog->witelog($text);
					//--------------------------
					
					return array(
                    'data_cat' => $data_cat,
                    'data_parent'=>$data_parent,
                    'alert' => $alert,
                    'data_manu' => $data_manu,
                    'data_detail' => $data_product,
                    'data_img' => $data_img,
                    'data_xuatxu' => $data_xuatxu,
                    'data_chatlieu' => $data_chatlieu,
					);
				}
				if ($checkname) {
					$obj_pro = new Product();
					$obj_pro->exchangeArray($data_product);
					$this->getProductTable()->updateproduct($id, $obj_pro);
					
					$alert = '<p class="bg-success">S???a s???n ph???m th??nh c??ng</p>';
					
					//Log File
					$witelog = new Utility();
					$text = 'S???a s???n ph???m c?? m?? l?? '.$code_id;
					$witelog->witelog($text);
					//--------------------------
					
					return array(
                    'data_cat' => $data_cat,
                    'data_parent'=>$data_parent,
                    'alert' => $alert,
                    'data_manu' => $data_manu,
                    'data_detail' => $data_product,
                    'data_img' => $data_img,
                    'data_xuatxu' => $data_xuatxu,
                    'data_chatlieu' => $data_chatlieu,
					);
					} else {
					$alert = '<p class="bg-warning"> M?? s???n ph???m n??y ???? t???n t???i kh??ng th??? th??m ???????c</p>';
					return array(
                    'data_detail' => $data_detail,
                    'data_cat' => $data_cat,
                    'data_parent'=>$data_parent,
                    'alert' => $alert,
                    'data_manu' => $data_manu,
                    'data_img' => $data_img,
                    'data_xuatxu' => $data_xuatxu,
                    'data_chatlieu' => $data_chatlieu,
					);
				}
			}
			return array(
            'data_detail' => $data_detail,
            'data_cat' => $data_cat,
            'data_parent'=>$data_parent,
            'data_manu' => $data_manu,
            'data_img' => $data_img,
            'data_xuatxu' => $data_xuatxu,
            'data_chatlieu' => $data_chatlieu,
			);
		}
		
		//Thay ?????i tr???ng th??i b??n h??ng, c??n h??ng ho???c h???t h??ng
		public function productstatusAction() {
			$id = $this->params()->fromPost('id');
			$status_pro = $this->params()->fromPost('status_pro');
			$data = array(
            'status_pro' => $status_pro,
			);
			$obj = new Product();
			$obj->exchangeArray($data);
			$this->getProductTable()->statuspro($id, $obj);
			
			//Log File
			$witelog = new Utility();
			$text = 'Thay ?????i tr???ng th??i s???n ph???m ID = '.$id;
			$witelog->witelog($text);
			//--------------------------
			echo 'Xuan Dac';
			die;
		}
		
		// Thay ?????i tr???ng th??i hi???n th??? ho???c kh??ng hi???n th???
		public function statusAction() {
			$id = addslashes(trim($this->params()->fromRoute('id', 0)));
			$status = addslashes(trim($this->params()->fromRoute('status', 0)));
			if ($status == 0) {
				$data = array('status' => 1);
				//Log File
                $text = 'Hi???n th??? s???n ph???m ID = '.$id;
                $this->witelog($text);
                //--------------------------
				} else {
				$data = array('status' => 0);
				//Log File
				$witelog = new Utility();
                $text = '???n s???n ph???m ID = '.$id;
				$witelog->witelog($text);
                //--------------------------
			}
			$obj = new Product();
			$obj->exchangeArray($data);
			$this->getProductTable()->changestatus($id, $obj);
			$this->redirect()->toRoute('Product');
		}
		
		// Thay ?????i tr???ng th??i hi???n th??? trang ch??? ho???c kh??ng
		public function showindexAction() {
			$id = addslashes(trim($this->params()->fromRoute('id', 0)));
			$showinex = addslashes(trim($this->params()->fromRoute('show', 0)));
			if ($showinex == 0) {
				$data = array('show_index' => 1);
				//Log File
				$witelog = new Utility();
                $text = 'Hi???n th??? s???n ph???m ra trang ch??? ID = '.$id;
				$witelog->witelog($text);
				//--------------------------
				} else {
				$data = array('show_index' => 0);
				//Log File
				$witelog = new Utility();
                $text = '???n s???n ph???m kh???i trang ch??? ID = '.$id;
				$witelog->witelog($text);
				//--------------------------
			}
			
			$obj = new Product();
			$obj->exchangeArray($data);
			$this->getProductTable()->change_showindex($id, $obj);
			$this->redirect()->toRoute('Product');
		}
		
		//cho s???n ph???m v??o th??ng r??c v?? kh??i ph???c l???i s???n ph???m
		public function trashAction() {
			$id = addslashes(trim($this->params()->fromRoute('id', 0)));
			$trash = $this->params()->fromRoute('trash', 0);
			if ($trash == 0) {
				$data = array('delete_pro' => 1);
				
				//Log File
				$witelog = new Utility();
                $text = 'Cho s???n ph???m v??o th??ng r??c ID = '.$id;
                $witelog->witelog($text);
				//--------------------------
				
				$router = $this->redirect()->toRoute('Product');
				} else {
				$data = array('delete_pro' => 0);
				
				//Log File
				$witelog = new Utility();
                $text = 'Kh??i ph???c s???n ph???m t??? th??ng r??c ID = '.$id;
                $witelog->witelog($text);
				//--------------------------
				
				$router = $this->redirect()->toUrl(WEB_PATH . '/system/product/trashproduct');
			}
			
			$obj = new Product();
			$obj->exchangeArray($data);
			$this->getProductTable()->product_trash($id, $obj);
			$this->$router;
		}
		
		//X??a v??nh vi???n s???n ph???m
		public function deleteproductAction() {
			$id = addslashes(trim($this->params()->fromRoute('id', 0)));
			$data_img = $this->getImageTable()->listimg($id);
			foreach ($data_img as $key => $value) {
				$url_img = WEB_MEDIA . '/media/images/' . $value['img'];
				$url_medium = WEB_MEDIA . '/media/images/' . $value['medium'];
				$url_thumb = WEB_MEDIA . '/media/images/' . $value['thumbnail'];
				unlink($url_img);
				unlink($url_medium);
				unlink($url_thumb);
			}
			$this->getImageTable()->delete_listimg($id); // x??a ???nh trong database
			$this->getProductTable()->deleteproduct($id); //X??a s???n ph???m trong database
			
			//Log File
			$witelog = new Utility();
			$text = 'X??a s???n ph???m ID = '.$id;
			$witelog->witelog($text);
			//--------------------------
			
			$this->redirect()->toRoute('Product');
		}
		
		
		
		//----------------------------------TRASH PRODUCT ---------------------------
		
		public function trashproductAction() {
			$this->layout('layout/user.phtml');
			$data = $this->getProductTable()->listproducttrash();
			foreach ($data as $key => $value) {
				$id_product = $value['id'];
				$data_img[$id_product] = $this->getImageTable()->loadimg_product($id_product);
			}
			return array('data' => $data, 'data_img' => @$data_img);
		}
		
		//X??a v??nh vi???n to??n b??? th??ng r??c
		public function deleteallAction() {
			$data_trash = $this->getProductTable()->listproducttrash(); //l???y ra danh s??ch c??c s???n ph???m trong th??ng r??c
			foreach ($data_trash as $key => $value) {
				$id_product = $value['id'];
				$data_img = $this->getImageTable()->listimg($id_product); // d??? li???u ???nh theo id s???n ph???m
				foreach ($data_img as $key1 => $value1) {
					$url_img = WEB_MEDIA . '/media/images/' . $value1['img'];
					$url_medium = WEB_MEDIA . '/media/images/' . $value1['medium'];
					$url_thumb = WEB_MEDIA . '/media/images/' . $value1['thumbnail'];
					unlink($url_img);
					unlink($url_medium);
					unlink($url_thumb);
				}
				$this->getImageTable()->delete_listimg($id_product); // x??a ???nh trong database
			}
			$this->getProductTable()->deleteall_trash(); //X??a to??n b??? s???n ph???m trong th??ng r??c
			
			//Log File
			$witelog = new Utility();
			$text = 'X??a s???ch th??ng r??c';
			$witelog->witelog($text);
			//--------------------------
			
			$this->redirect()->toUrl(WEB_PATH . '/system/product/trashproduct');
		}
		
		// ---------------------------------AJAX IMG---------------------------------
		public function uploadimgAction() {
			$this->layout('layout/user.phtml');
			$id = $this->params()->fromPost('id_pro');
			// Upload ???nh
			$dirpath = str_replace("\\", "/", WEB_MEDIA . "/media/images/imgProduct");
			$tmpimg = $_FILES["img"]["tmp_name"];
			//echo $tmpimg;die;
			$filename = $_FILES["img"]["name"];
			$ext = substr(strrchr($filename, '.'), 1);
			$fileupload =  time($filename) . '.' . $ext;
			if ($filename == null) {
				echo 'error';
				die;
			}
			/*if (move_uploaded_file($tmpimg, "$dirpath/images/$fileupload")) {
				$resizeObj = new Resize("$dirpath/images/$fileupload");
				$resizeObj->resizeImage(170, 208, 'crop');
				$resizeObj->saveImage($dirpath . '/thumb/' . $fileupload, 100);
				
				$resizeObj->resizeImage(398, 398, 'crop');
				$resizeObj->saveImage($dirpath . '/medium/' . $fileupload, 100);
			}*/
			$Uty = new Utility();
			copy($tmpimg, $dirpath . '/thumb/' . $fileupload);
			$Uty->load($tmpimg);					
			$Uty->resizeToWidth(171);
			$Uty->save($dirpath . '/thumb/' . $fileupload); // ???nh thumb
			
			copy($tmpimg, $dirpath . '/medium/' . $fileupload);
			$Uty->load($tmpimg);					
			$Uty->resizeToWidth(398);
			$Uty->save($dirpath . '/medium/' . $fileupload); // ???nh medium
			
			move_uploaded_file($tmpimg, $dirpath.'/images/'.$fileupload); //anhr g???c
			
			// L??u th??ng tin ???nh v??o db
			$data_img = array(
            'id_product' => $id,
            'img' => 'imgProduct/images/' . $fileupload,
            'medium' => 'imgProduct/medium/' . $fileupload,
            'thumbnail' => 'imgProduct/thumb/' . $fileupload,
            'status' => '0'
			);
			$obj_img = new Image();
			$obj_img->exchangeArray($data_img);
			$this->getImageTable()->addimg($obj_img);
			
			//Log File
			$witelog = new Utility();
			$text = 'Upload ???nh cho s???n ph???m ID = '.$id;
			$witelog->witelog($text);
			//--------------------------
			
			// l???y ???nh v???a up append v??o table
			$img_new = $this->getImageTable()->getimg_new();
			if ($img_new['status'] == 1) {
				$status = 'checked';
				} else {
				$status = '';
			}
			echo '<tr id="tr-img' . $img_new['id'] . '">'
			. '<td>'
			. '<img src="' . WEB_IMG .'images/'. $img_new['thumbnail'] . '" width="80" onerror=this.src="' . WEB_IMG . 'images/imgdefault.jpg" />'
			. '</td>'
			. '<td>'
			. '<input type="radio" onclick="changstatus(' . $img_new['id'] . ')" name="img-pro" ' . $status . '/>'
			. '</td>'
			. '<td>'
			. '<span onclick="removeimg(' . $img_new['id'] . ')" class="btn btn-danger btn-xs"><i class="icon-trash"></i></span>'
			. '</td>
			</tr>';
			die;
		}
		
		public function changestatusimgAction() {
			$id_img = $this->params()->fromPost('id_img');
			$img_detail = $this->getImageTable()->imgdetail($id_img);
			$id_pro = $img_detail['id_product'];
			$reset = 0;
			$data = array('status' => $reset);
			$objst = new Image();
			$objst->exchangeArray($data);
			$this->getImageTable()->resetStatus($id_pro, $objst); // Reset t???t c??? ???nh c???a 1 s???n ph???m v??? 0
			
			$datanew = array('status' => 1);
			$objnew = new Image();
			$objnew->exchangeArray($datanew);
			$this->getImageTable()->changestatus($id_img, $objnew);    // Active pubid v???a l???y;    
			
			//Log File
			$witelog = new Utility();
			$text = 'Ch???n ???nh hi???n th??? cho s???n ph???m ID = '.$id_pro;
			$witelog->witelog($text);
			//--------------------------
			echo 'Xuan Dac';
			die;
		}
		
		public function removeimgAction() {
			$id_img = $this->params()->fromPost('id_img');
			$image_detail = $this->getImageTable()->imgdetail($id_img);
			$url_img = WEB_MEDIA . '/media/images/' . $image_detail['img'];
			$url_medium = WEB_MEDIA . '/media/images/' . $image_detail['medium'];
			$url_thumb = WEB_MEDIA . '/media/images/' . $image_detail['thumbnail'];
			unlink($url_img);
			unlink($url_medium);
			unlink($url_thumb);
			$this->getImageTable()->removeimg($id_img);	
			
			//Log File
			$witelog = new Utility();
			$text = 'X??a ???nh s???n ph???m ID = '.$image_detail['id_product'];
			$witelog->witelog($text);
			//--------------------------
			echo 'Xuan Dac';
		die;
		}
		
		
		
		}
		
		?>					