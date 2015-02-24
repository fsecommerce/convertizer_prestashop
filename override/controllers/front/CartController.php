<?php
/*
* FS eCommerce GmbH 2015
*/

class CartController extends CartControllerCore{
	public function init(){
		parent::init();
		$this->addbysku();
	}
	
	protected function addbysku(){
		if ($_GET['action']=='addbysku'){
			$sku 		= $_GET['sku'];
			$sql 		= "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '$sku'";
			$product_id = Db::getInstance()->getValue($sql,$use_cache = false);
			if($product_id){
				$product = new Product ($product_id, $this->context->language->id);
				if(count($product->getAttributeCombinations($this->context->language->id))){
					Tools::redirect("index.php?controller=product&id_product=$product_id");
				}
				try{
					if(!$this->context->cart->id){
					    if (Context::getContext()->cookie->id_guest){
				        	$guest = new Guest(Context::getContext()->cookie->id_guest);
						    $this->context->cart->mobile_theme = $guest->mobile_theme;
						}
						$this->context->cart->add();
						if ($this->context->cart->id){
							$this->context->cookie->id_cart = (int)$this->context->cart->id;
						}
						$cart	= new Cart($this->context->cart->id);
					}else{
						$cart	= $this->context->cart;
					}
					$cart->updateQty(1, $product_id, null, false);
					Tools::redirect('index.php?controller=cart');
				}catch(Exception $e){
					Tools::redirect('index.php');
				}
			}else{
				Tools::redirect('index.php');
			}
		}
	}
}
