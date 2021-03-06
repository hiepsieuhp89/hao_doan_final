<?php

namespace Fronts\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Customer\Model\Customer;
use Product\Model\Utility;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class AcountController extends AbstractActionController {

    protected $Acount;

    public function getAcountTable() {
        if (!$this->Acount) {
            $pst = $this->getServiceLocator();
            $this->Acount = $pst->get('Customer\Model\CustomerTable');
        }
        return $this->Acount;
    }

    protected $Banner;

    public function getBannerTable() {
        if (!$this->Banner) {
            $pst = $this->getServiceLocator();
            $this->Banner = $pst->get('Banner\Model\BannerTable');
        }
        return $this->Banner;
    }

    protected $Category;

    public function getProductcategoryTable() {
        if (!$this->Category) {
            $pst = $this->getServiceLocator();
            $this->Category = $pst->get('Product\Model\ProductTablecategory');
        }
        return $this->Category;
    }

    protected $Order;

    public function getOrderTable() {
        if (!$this->Order) {
            $sm = $this->getServiceLocator();
            $this->Order = $sm->get('Invoice\Model\OderTable');
        }
        return $this->Order;
    }

    protected $Orderdetail;

    public function getOrderDetailTable() {
        if (!$this->Orderdetail) {
            $sm = $this->getServiceLocator();
            $this->Orderdetail = $sm->get('Invoice\Model\OderdetailTable');
        }
        return $this->Orderdetail;
    }

    protected $Product;

    public function getProductTable() {
        if (!$this->Product) {
            $pst = $this->getServiceLocator();
            $this->Product = $pst->get('Product\Model\ProductTable');
        }
        return $this->Product;
    }

    public function loginAction() {
        $this->getlayout();
        $title_page = '<li><a href="#"><span>T??i kho???n c???a t??i</span></a></li>
		   <li><a href=""><span>????ng nh???p h??? th???ng</span></a></li>';
        $this->layout()->setVariable('title_page', $title_page);

        if ($this->request->isPost()) {
            $email = addslashes(trim($this->params()->fromPost('email')));
            $password = addslashes(trim($this->params()->fromPost('password')));
            $endpass = base64_encode(md5(base64_encode(md5($password))));
            $checklogin = $this->getAcountTable()->checklogin($email, $endpass);
            if ($checklogin) {
                $data_detail = $this->getAcountTable()->acountdetail_email($email);
                if ($data_detail['status'] == 1) {
                    $session_user = new Container('userlogin');
                    $session_user->username = $data_detail['fullname'];
                    $session_user->idus = $data_detail['id'];
                    $session_user->email = $data_detail['email'];
                    $session_user->address = $data_detail['address'];
                    $session_user->phone = $data_detail['phone'];
                    $this->redirect()->toUrl(WEB_PATH);
                } else {
                    $error = "T??i kho???n c???a b???n ??ang b??? kh??a h??y li??n h??? v???i qu???n tr??? website ????? bi???t th??ng tin chi ti???t";
                    return array('error' => $error);
                }
            } else {
                $error = "Email ho???c M???t kh???u kh??ng ????ng";
                return array('error' => $error);
            }
        }
    }

    public function registerAction() {
        $this->getlayout();
        //load email h??? th???ng
        $session_email = new Container('emailsystem');
        $email_admin = $session_email->email_admin;
        $title_page = '<li><a href="#"><span>T??i kho???n c???a t??i</span></a></li>
		   <li><a href=""><span>????ng k?? t??i kho???n </span></a></li>';
        $this->layout()->setVariable('title_page', $title_page);

        //print_r($country);die;
        if ($this->request->isPost()) {
            $name = addslashes(trim($this->params()->fromPost('fullname')));
            $email = addslashes(trim($this->params()->fromPost('email')));
            $phone = addslashes(trim($this->params()->fromPost('phone')));
            $password = addslashes(trim($this->params()->fromPost('password')));
            $company = addslashes(trim($this->params()->fromPost('company')));
            $address = addslashes(trim($this->params()->fromPost('address')));
            $date = date('Y-m-d');
            $endpass = base64_encode(md5(base64_encode(md5($password))));
            $data = array(
                'fullname' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $endpass,
                'company' => $company,
                'address' => $address,
                'yahoo' => '',
                'skype' => '',
                'facebook' => '',
                'date' => $date,
                'cus_mod' => $date
            );

            $check = $this->getAcountTable()->checkacount($email);
            if ($check) {
                $objct = new Customer();
                $objct->exchangeArray($data);
                $this->getAcountTable()->addacount($objct);


                /// G???i Mail khi ????ng k?? th??nh c??ng
                $title_mail = 'Giadung88.com | Th??ng b??o Qu?? Kh??ch h??ng  ???? ????ng k?? th??nh c??ng t??i kho???n mua h??ng m???i';
                $content_mail = '<p style="font-weight:bold">Xin ch??o b???n, ' . $name . '</p>
                               <p>Th??ng tin t??i kho???n c???a b???n t???i <span style="font-weight:bold">Giadung88.com</span></p>
                               <p style="font-weight:bold">Email ????ng nh???p: ' . $email . '</p>
                               <p style="font-weight:bold">M???t kh???u: ' . $password . '</p>
                               <p>H??y truy c???p Website <a href="' . WEB_PATH . '">' . WEB_PATH . '</a> ????? ????ng nh???p</p>';
                $this->sendmail($email, $email_admin, $title_mail, $content_mail);

                $user_new = $this->getAcountTable()->get_acount_new();
                $session_user = new Container('userlogin');
                $session_user->username = $user_new['fullname'];
                $session_user->idus = $user_new['id'];
                $session_user->email = $user_new['email'];
                $session_user->address = $user_new['address'];
                $session_user->phone = $user_new['phone'];
                $this->redirect()->toUrl(WEB_PATH);
            } else {
                $error = 'Email n??y ???? t???n t???i kh??ng th??? ????ng k??';
                return array('error' => $error);
            }
        }
    }

    public function personalAction() {
        $this->getlayout();
        $checklogin = new Customer();
        $checklogin->checklogin();
        $title_page = '<li><a href="' . WEB_PATH . '/tai-khoan/personal.html"><span>T??i kho???n c???a t??i</span></a></li>';
        $this->layout()->setVariable('title_page', $title_page);
    }

    public function informationAction() {
        $this->getlayout();
        $checklogin = new Customer();
        $checklogin->checklogin();
        $title_page = '<li><a href="#"><span>T??i kho???n c???a t??i</span></a></li>
                 <li><a href=""><span>Th??ng tin t??i kho???n</span></a></li>';
        $this->layout()->setVariable('title_page', $title_page);
        $session_user = new Container('userlogin');
        $id_us = $session_user->idus;
        $data_acount = $this->getAcountTable()->acountdetail($id_us);
        if ($this->request->isPost()) {
            $name = addslashes(trim($this->params()->fromPost('name')));
            $email = addslashes(trim($this->params()->fromPost('email')));
            $password = addslashes(trim($this->params()->fromPost('password')));
            $phone = addslashes(trim($this->params()->fromPost('phone')));
            $company = addslashes(trim($this->params()->fromPost('company')));
            $address = addslashes(trim($this->params()->fromPost('address')));
            $city = addslashes(trim($this->params()->fromPost('city')));
            $yahoo = addslashes(trim($this->params()->fromPost('yahoo')));
            $skype = addslashes(trim($this->params()->fromPost('skype')));
            $facebook = addslashes(trim($this->params()->fromPost('facebook')));
            $date = date('Y-m-d');

            //?????i pass
            if ($password != null) {
                $endpass = base64_encode(md5(base64_encode(md5($password))));
                $data_passnew = array(
                    'password' => $endpass
                );
                $obj = new Customer();
                $obj->exchangeArray($data_passnew);
                $this->getAcountTable()->updatepass_user($id_us, $obj);
                $error2 = " C???p nh???t m???t kh???u th??nh c??ng";
                return array('error2' => $error2, 'data_acount' => $data_acount);
            }

            // Thay ?????i th??ng tin
            $data = array(
                'fullname' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $data_acount['password'],
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'yahoo' => $yahoo,
                'skype' => $skype,
                'facebook' => $facebook,
                'date' => $data_acount['date'],
                'cus_mod' => $date
            );
            if ($email == $data_acount['email']) {
                $obj = new Customer();
                $obj->exchangeArray($data);
                $this->getAcountTable()->update_acount($id_us, $obj);
                $error = "C???p nh???t th??ng tin t??i kho???n th??nh c??ng";
                return array('data_acount' => $data_acount, 'error' => $error);
            } else {
                $check = $this->getAcountTable()->checkacount($email);
                if ($check) {
                    $obj = new Customer();
                    $obj->exchangeArray($data);
                    $this->getAcountTable()->update_acount($id_us, $obj);
                    $error = "C???p nh???t th??ng tin t??i kho???n th??nh c??ng";
                    return array('data_acount' => $data_acount, 'error' => $error);
                } else {
                    $error1 = "Email n??y ???? t???n t???i kh??ng th??? c???p nh???t";
                    return array('data_acount' => $data_acount, 'error1' => $error1);
                }
            }
        }
        return array('data_acount' => $data_acount);
    }

    public function resetpassAction() {
        $this->getlayout();
        //load email h??? th???ng
        $session_email = new Container('emailsystem');
        $email_admin = $session_email->email_admin;
        $title_page = '<li><a href="#"><span>T??i kho???n c???a t??i</span></a></li>
                 <li><a href="#"><span> Kh??i ph???c m???t kh???u</span></a></li>';
        $this->layout()->setVariable('title_page', $title_page);
        if ($this->request->isPost()) {
            $email = addslashes(trim($this->params()->fromPost('email')));
            $check = $this->getAcountTable()->checkacount($email);
            if ($check) {
                $error = 'Email n??y ch??a ???????c ????ng k??';
                return array('error' => $error);
            } else {
                $Uty = new Utility();
                $pass = $Uty->rand_string(8);
                $endpass = base64_encode(md5(base64_encode(md5($pass))));
                $data = array(
                    'password' => $endpass
                );
                $obj = new Customer();
                $obj->exchangeArray($data);
                $this->getAcountTable()->updatepass($email, $obj);

                $title_mail = 'Giadung88.com | Kh??i ph???c m???t kh???u';
                $content_mail = '<p>B???n v???a s??? d???ng t??nh n??ng kh??i ph???c m???t kh???u Website <a href="' . WEB_PATH . '"><span style="font-weight:bold">Giadung88.com</span></a></p>
                    <p style="font-weight:bold"> M???t kh???u m???i c???a b???n l??: ' . $pass . '</p>
                    <p>????? b???o m???t t??i kho???n c???a b???n h??y ????ng nh???p Website <a href="' . WEB_PATH . '"><span style="font-weight:bold">Giadung88.com</span></a> ????? ?????i m???t kh???u c???a b???n</p>';
                $this->sendmail($email, $email_admin, $title_mail, $content_mail);

                $error1 = 'M???t m???t kh???u m???i ???? ???????c ch??ng t??i g???i ?????n Email c???a b???n. Xin vui l??ng ki???m tra Email c???a b???n !';
                return array('error1' => $error1);
            }
        }
    }

    public function orderAction() {
        $this->getlayout();
        $checklogin = new Customer();
        $checklogin->checklogin();
        $title_page = '<li><a href="#"><span>T??i kho???n c???a t??i</span></a></li>
                 <li><a href="#"><span> Qu???n l?? ????n h??ng</span></a></li>';
        $this->layout()->setVariable('title_page', $title_page);
        $session_user = new Container('userlogin');
        $id_customer = $session_user->idus;

        $select = new Select();
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $data_order = $this->getOrderTable()->getoder_user($id_customer);

        $itemsPerPage = 3;
        $data_order->current();
        $paginator = new Paginator(new paginatorIterator($data_order));
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(5);

        if ($paginator != null) {
            foreach ($data_order as $key => $value) {
                $id_order = $value->id;
                //echo $id_order;
                $data_oder_detai[$id_order] = $this->getOrderDetailTable()->get_array_order($id_order);
                foreach ($data_oder_detai[$id_order] as $key1 => $value1) {
                    $id_product = $value1['id_product'];
                    $listproduct[$id_product] = $this->getProductTable()->product_shoppingcart($id_product);
                }
            }
            return array(
                'paginator' => $paginator,
                'oder_detail' => $data_oder_detai,
                'listproduct' => $listproduct,
            );
        }// n???u c?? h??a ????n m???i ch???y ??o???n n??y
        return array(
            'paginator' => $paginator,
        );
    }

    public function logoutAction() {
        $this->getlayout();
        $session_user = new Container('userlogin');
        $session_user->offsetUnset('username');
        $session_user->offsetUnset('idus');
        $session_user->offsetUnset('email');
        $session_user->offsetUnset('address');
        $session_user->offsetUnset('phone');

        //-----------------------

        $session_customer_guest = new Container('customer_guest');
        $session_customer_guest->offsetUnset('name_customer');
        $session_customer_guest->offsetUnset('mail_customer');
        $session_customer_guest->offsetUnset('phone_customer');
        $session_customer_guest->offsetUnset('address_customer');
        
        $this->redirect()->toUrl(WEB_PATH);
    }

    public function sendmail($mail_to, $mail_from, $title_mail, $content_mail) {
        //load email h??? th???ng
        $session_email = new Container('emailsystem');
        $mail_system = $session_email->email_system;
        $pass_system = $session_email->pass_system;
        //G???i ???????c c??? html v?? text
        $message = new Message();
        $message->addTo($mail_to)//Email nh???n
                ->addFrom($mail_from)//Email g???i
                ->setSubject($title_mail); //Ti??u ????? mail
        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            // 'name' => 'localhost',
            'host' => 'smtp.gmail.com',
            'connection_class' => 'login',
            'connection_config' => array(
                'ssl' => 'tls',
                'username' => $mail_system,
                'password' => $pass_system
            ),
            'port' => 587,
        ));
        $content = $content_mail; // N???i dung Email
        $html = new MimePart($content);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $message->setBody($body);

        $transport->setOptions($options);
        $transport->send($message);
    }

    public function getlayout() {
        $this->layout('layout/layoutitem.phtml');
        $show_banner = 1;
        $filter = 'false';
        $data_cat = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'cat',));
        $data_parent = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'catparent',));
        $product_left = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'productleft',));

        $data_banner = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'banner',));
        $acticre = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'acticre',));
        $setting = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'setting',));
        $brand = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'brand',));
        $xuatxu = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'xuatxu',));
        $chatlieu = $this->forward()->dispatch('Fronts\Controller\Elements', array(
            'action' => 'chatlieu',));

        //seo
        $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set(stripcslashes($setting->seo_title));
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headMeta()->setName('keywords', stripcslashes($setting->seo_keyword));
        $renderer->headMeta()->setName('news_keywords', stripcslashes($setting->seo_keyword));
        $renderer->headMeta()->setName('description', stripcslashes($setting->seo_description));

        $this->layout()->setVariable('filter', $filter);
        $this->layout()->setVariable('show_banner', $show_banner);
        $this->layout()->setVariable('data_cat', $data_cat);
        $this->layout()->setVariable('data_parent', $data_parent);
        $this->layout()->setVariable('product_left', $product_left);
        $this->layout()->setVariable('data_banner', $data_banner);
        $this->layout()->setVariable('acticre', $acticre);
        $this->layout()->setVariable('setting', $setting);
        $this->layout()->setVariable('brand', $brand);
        $this->layout()->setVariable('xuatxu', $xuatxu);
        $this->layout()->setVariable('chatlieu', $chatlieu);
    }

    //--------------------------------------
}

?>