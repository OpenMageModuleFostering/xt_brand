<?php 

class XT_Brand_Adminhtml_BrandController extends Mage_Adminhtml_Controller_action
{

	public function gridAction(){
		$this->loadLayout();
		$this->getLayout()->getBlock('customer.grid');
		$this->renderLayout();
	}
	
	public function upsellgridAction(){
		$this->loadLayout();
		$this->getLayout()->getBlock('customer.grid');
		$this->renderLayout();
	}


	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('brand/items')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Brand'), Mage::helper('adminhtml')->__('Item Manager'));

		return $this;
	}

	public function indexAction() {
		$this->_initAction()
		->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('brand/brand')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('brand_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('brand/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('brand/adminhtml_brand_edit'))
			->_addLeft($this->getLayout()->createBlock('brand/adminhtml_brand_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brand')->__('Item does not exist'));
			$this->_redirect('*/*/edit', array('id' => 1));
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'brand' . DS;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
			
			//print_r($data); die();
			$_postId = $this->getRequest()->getParam('id');
			$is_edit = 0;
			if(isset($_postId))
			{
				$is_edit = 1;
				$model = Mage::getModel('brand/brand')->load($_postId);
				$model->setTitle($data['title']);
				if(isset($data['filename'])) {
				 $model->setFilename($data['filename']);
				}				
				$model->setDescription($data['description']);
				
				//$_requestPath=mysql_real_escape_string( strtolower($data['brand_url']) );
				$_requestPath = strtolower($data['brand_url']);
				$_requestPath = str_replace(' ','-',$_requestPath);				
				$data['brand_url'] = $_requestPath;
				$model->setBrandUrl($data['brand_url']);				
			}
			else
			{
				$model = Mage::getModel('brand/brand');
				$model->setTitle($data['title']);
				if(isset($data['filename'])) {
				 $model->setFilename($data['filename']);
				}
				$model->setDescription($data['description']);
				
				//$_requestPath=mysql_real_escape_string( strtolower($data['brand_url']) );
				$_requestPath = strtolower($data['brand_url']);
				$_requestPath = str_replace(' ','-',$_requestPath);				
				$data['brand_url'] = $_requestPath;
				$model->setBrandUrl($data['brand_url']);				
			}

			try {

				$model->save();
				if($is_edit == 1)
				{
				   	$urlRewriteCollection = Mage::getModel('core/url_rewrite')->getCollection()
					->addFieldToFilter('target_path',array('eq'=>'brand/index/index/id/'.$_postId))->load();
					foreach ($urlRewriteCollection as $urlRewrite)
					 {
					   $urlRewrite->delete();
					 }
				}
				Mage::getModel('core/url_rewrite')->setIsSystem(0)->setStoreId(Mage::app()->getStore()->getId())
						->setOptions('')
						->setIdPath(time())
						->setTargetPath('brand/index/index/id/'.$model->getId())
						->setRequestPath('brand/'.$_requestPath)
						->save();
				$brand_id = $model->getId();
				if(isset($data['links'])){
					$customers = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['customers']); //Save the array to your database

					$collection = Mage::getModel('brand/grid')->getCollection();
					$collection->addFieldToFilter('brand_id',$brand_id);
					$collection->addFieldToFilter('store_id',$data['store_id']);
					foreach($collection as $obj){
						$obj->delete();
					}
					
					foreach($customers as $key => $value){
						$model2 = Mage::getModel('brand/grid');
						$model2->setBrandId($brand_id);
						$model2->setProductId($key);
						$model2->setPosition($value['position']);
						$model2->setStoreId($data['store_id']);
						$model2->save();
					}
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brand')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $model->getId()));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brand')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('brand/brand');

				$model->setId($this->getRequest()->getParam('id'))
				->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$brandIds = $this->getRequest()->getParam('brand');
		if(!is_array($brandIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		} else {
			try {
				foreach ($brandIds as $brandId) {
					$brand = Mage::getModel('brand/brand')->load($brandId);
					$brand->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($brandIds)
				)
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function setregisterAction() {
		$store_id = $this->getRequest()->getParam('store_id');
		Mage::getSingleton('adminhtml/session')->setData('brand_store_id',$store_id);
	}

}