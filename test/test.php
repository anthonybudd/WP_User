<?php
// Dear Reader,
// 
// I am acutely aware how bad this code is for testing
// but, it did the job when I couldn't set-up WP unit testing.
// 
// 
// P.S Star the rep
// 
// Anthony


add_action('init', 'runTests');


$events = [
	'booting' 		=> FALSE,
	'booted' 		=> FALSE,
	'saving' 		=> FALSE,
	'inserting' 	=> FALSE,
	'inserted' 		=> FALSE,
	'saved' 		=> FALSE,
	'deleting' 		=> FALSE,
	'deleted' 		=> FALSE,
	'serializing' 	=> FALSE,
];

function setup(){
	if(!class_exists('User')){
		require 'User.php';
	}
}

function runTests(){
	setup();

	global $wpdb;
	$del = $wpdb->query("TRUNCATE TABLE $wpdb->posts");
	$del = $wpdb->query("TRUNCATE TABLE $wpdb->postmeta");

	// test();

	dd('SUCCESS!');
}

function error($debug, $info = '!!!'){
	echo '<h1 style="color:red;"> Error: '. $debug .'</h1>';

	if($info !== '!!!'){
		dump($info);
	}

	echo '<hr>';
}


function test(){

	// -----------------------------------------------------
	// PROPERTIES
	// -----------------------------------------------------
		$product = Product::insert([
			'title' => 'product',
		    'weight' => '250'
		]);

		if(! ($product->color === 'black') ){
			error(__LINE__ .' $default', $product);
		}

		// Filter
		$product = Product::insert([
		    'weight' => '250',
		    'stock_left' => '50',
		    'items_sold' => '5000'
		]);

		if(! (is_int($product->weight)) ){
			error(__LINE__ .' $default');
		}

		if(! ($product->weight === 250) ){
			error(__LINE__ .' $default');
		}

		if(! (is_int($product->stock_left)) ){
			error(__LINE__ .' $default');
		}

		if(! ($product->items_sold === '5,000') ){
			error(__LINE__ .' $default');
		}

	// -----------------------------------------------------
	// VIRTUAL PROPERTIES
	// -----------------------------------------------------
		$product = Product::insert([
			'title' => 'title',
		    'content' => 'content'
		]);

		if(! ($product->title === 'title') ){
			error(__LINE__ .' VIRTUAL PROPERTIES');
		}

		if(! ($product->content === 'content') ){
			error(__LINE__ .' VIRTUAL PROPERTIES');
		}

		if(! ($product->the_content === "<p>content</p>\n") ){
			error(__LINE__ .' VIRTUAL PROPERTIES');
		}

		$product->save();
		$product = Product::find($product->ID);

		if(! ($product->title === 'title') ){
			error(__LINE__ .' VIRTUAL PROPERTIES');
		}

		if(! ($product->content === 'content') ){
			error(__LINE__ .' VIRTUAL PROPERTIES');
		}

		if(! ($product->the_content === "<p>content</p>\n") ){
			error(__LINE__ .' VIRTUAL PROPERTIES');
		}

	// -----------------------------------------------------
	// EVENTS
	// -----------------------------------------------------

		// ---- triggerEvent()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product = Product::find($product->ID);
			$product->save();
			$product->delete();
			$product::restore($product->ID);
			$product->hardDelete();

			global $events;
			if(!($events['booting'])){ 	error(__LINE__ .' booting Event');}
			if(!($events['booted'])){ error(__LINE__ .' booted Event');}
			if(!($events['saving'])){ error(__LINE__ .' saving Event');}
			if(!($events['inserting'])){ error(__LINE__ .' inserting Event');}
			if(!($events['inserted'])){ error(__LINE__ .' inserted Event');}
			if(!($events['saved'])){        error(__LINE__ .' saved Event');}
			if(!($events['deleting'])){     error(__LINE__ .' deleting Event');}
			if(!($events['deleted'])){      error(__LINE__ .' deleted Event');}
			if(!($events['serializing'])){  error(__LINE__ .' serializing Event');}


	// -----------------------------------------------------
	// HOOKS
	// -----------------------------------------------------
		// ---- addHooks()
		
		// ---- removeHooks()
		
		// ---- onSave()
		
		
	// -----------------------------------------------------
	// UTILITY METHODS
	// -----------------------------------------------------
	
		// ---- getPostType()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->getPostType() === 'product') ){
				error(__LINE__ .' getPostType()');
			}

		// ---- newInstance()
			if(! (is_object(Product::newInstance())) ){
				error(__LINE__ .' getPostType()');
			}
		 
		// ---- jsonSerialize()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$json = json_encode($product);
			switch(json_last_error()){
			    case JSON_ERROR_NONE:
			        // SUCCESS
			    break;
			    case JSON_ERROR_DEPTH:
			        error(__LINE__ .' jsonSerialize() - Maximum stack depth exceeded');
			    break;
			    case JSON_ERROR_STATE_MISMATCH:
			        error(__LINE__ .' jsonSerialize() - Underflow or the modes mismatch');
			    break;
			    case JSON_ERROR_CTRL_CHAR:
			        error(__LINE__ .' jsonSerialize() - Unexpected control character found');
			    break;
			    case JSON_ERROR_SYNTAX:
			        error(__LINE__ .' jsonSerialize() - Syntax error, malformed JSON');
			    break;
			    case JSON_ERROR_UTF8:
			        error(__LINE__ .' jsonSerialize() - Malformed UTF-8 characters, possibly incorrectly encoded');
			    break;
			    default:
			        error(__LINE__ .' jsonSerialize() - Unknown error');
			    break;
			}
		 
		// ---- getMeta()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->getMeta('color') === 'blue') ){
				error(__LINE__ .' getMeta()');
			}

		// ---- setMeta()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product->setMeta('color', 'red');

			if(! ($product->getMeta('color') === 'red') ){
				error(__LINE__ .' setMeta()');
			}
		 
		// ---- deleteMeta()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product->deleteMeta('color');

			if(! ($product->getMeta('color') === '') ){
				error(__LINE__ .' deleteMeta()');
			}
		
	// -----------------------------------------------------
	// GETTERS & SETTERS
	// -----------------------------------------------------

		// ---- get()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->get('color') === 'blue') ){
				error(__LINE__ .' get()');
			}

			if(! ($product->get('DEFAULT', 'EMPTY') === 'EMPTY') ){
				error(__LINE__ .' get()');
			}

		// ---- set()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product->set('color', 'red');

			if(! ($product->color === 'red') ){
				error(__LINE__ .' get()');
			}

		// ---- getTaxonomy()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			if(! (is_array($product->getTaxonomy('category'))) ){
				error(__LINE__ .' getTaxonomy()');
			}

			if(! ($product->getTaxonomy('category')[0] INSTANCEOF WP_Term) ){
				error(__LINE__ .' getTaxonomy()');
			}
			
		// ---- addTaxonomy()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			]);

			$product->addTaxonomy('category', 2);
			$product->addTaxonomy('category', 'office');

			if(! (is_array($product->getTaxonomy('category'))) ){
				error(__LINE__ .' getTaxonomy()');
			}

			if(! ($product->getTaxonomy('category')[0] INSTANCEOF WP_Term) ){
				error(__LINE__ .' getTaxonomy()');
			}

		// ---- addTaxonomies()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			]);

			$product->addTaxonomies('category', [2, 3]);

			if(! (is_array($product->getTaxonomy('category'))) ){
				error(__LINE__ .' addTaxonomies()');
			}

			if(! ($product->getTaxonomy('category')[0] INSTANCEOF WP_Term) ){
				error(__LINE__ .' addTaxonomies()');
			}


		// ---- clearTaxonomy()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			$product->clearTaxonomy('category');

			if(! (is_array($product->getTaxonomy('category'))) ){
				error(__LINE__ .' clearTaxonomy()');
			}

			if(! (count($product->getTaxonomy('category')) == 0) ){
				error(__LINE__ .' clearTaxonomy()');
			}

		// ---- removeTaxonomy()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			$product->removeTaxonomy('category', 'home');
			$product->removeTaxonomy('category', 7);

			if(! (count($product->category) === 0) ){
				error(__LINE__ .' removeTaxonomy()');
			}
		
		// ---- removeTaxonomies()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			$product->removeTaxonomies('category', ['home', 7]);

			if(! (count($product->category) === 0) ){
				error(__LINE__ .' removeTaxonomies()');
			}


			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			$product->removeTaxonomies('category', ['home', 'office']);

			if(! (count($product->category) === 0) ){
				error(__LINE__ .' removeTaxonomies()');
			}
		
		// ---- isVirtualProperty()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->isVirtualProperty('available') === TRUE) ){
				error(__LINE__ .' isVirtualProperty()');
			}

		// ---- getVirtualProperty()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->getVirtualProperty('available') === 'virtual') ){
				error(__LINE__ .' getVirtualProperty()');
			}

			if(! ($product->available === 'virtual') ){
				error(__LINE__ .' getVirtualProperty()');
			}

	// -----------------------------------------------------
	// HELPER METHODS
	// -----------------------------------------------------

		// ---- exists()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! (Product::exists($product->ID) === TRUE) ){
				error(__LINE__ .' exists()');
			}

			if(! (Product::exists(0) === FALSE) ){
				error(__LINE__ .' exists()');
			}

		// ---- count()
			if(! ( is_int(Product::count())) ){
				error(__LINE__ .' count()');
			}

			if(! ( is_int(Product::count('publish'))) ){
				error(__LINE__ .' count()');
			}

			if(! ( Product::count('draft') == 0) ){
				error(__LINE__ .' count()');
			}
	
		// ---- post()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->post() INSTANCEOF WP_Post) ){
				error(__LINE__ .' post()');
			}
	
		// ---- hasFeaturedImage()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->hasFeaturedImage() === FALSE) ){
				error(__LINE__ .' hasFeaturedImage()');
			}
	
		// ---- featuredImage()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! ($product->featuredImage('DEFAULT') === 'DEFAULT') ){
				error(__LINE__ .' featuredImage()');
			}
	
		// ---- toArray()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! (is_array($product->toArray())) ){
				error(__LINE__ .' toArray()');
			}

			if(! ($product->toArray()['ID'] === $product->ID) ){
				error(__LINE__ .' toArray()');
			}
			
	
		// ---- single()
	
		// ---- permalink()

		// ---- postDate()
		$product = Product::insert([
			'title' => 'product',
		    'color' => 'blue',
		    'weight' => '250'
		]);

		if(! ($product->postDate() === date('d-m-Y')) ){
			error(__LINE__ .' postDate()');
		}
			

	// -----------------------------------------------------
	// MAGIC METHODS
	// -----------------------------------------------------
	
		// ---- __set()
			$product = Product::insert([
				'title' => 'product',
			    'color' => 'blue',
			    'weight' => '250',
			]);

			$product->color = 'red';

			if(! ($product->color == 'red') ){
				error(__LINE__ .' __set()');
			}

			if(! ($product->dirty === TRUE) ){
				error(__LINE__ .' $dirty');
			}
	
		// ---- __get()
			$product = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			if(! ($product->color == 'blue') ){
				error(__LINE__ .' __get()');
			}

			if(! (is_array($product->category)) ){
				error(__LINE__ .' __get()');
			}

			if(! ($product->category[0] == 'Home') ){
				error(__LINE__ .' __get()');
			}

			if(! ($product->available === 'virtual') ){
				error(__LINE__ .' __get()');
			}

			if(! ($product->title === 'product') ){
				error(__LINE__ .' __get()');
			}

			if(! ($product->content === 'content') ){
				error(__LINE__ .' __get()');
			}
	

	// -----------------------------------------------------
	// FINDERS
	// -----------------------------------------------------

		// ---- find()
			$product = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! (Product::find($product->ID) !== NULL) ){
				error(__LINE__ .' find()');
			}

			if(! (is_object(Product::find($product->ID))) ){
				error(__LINE__ .' find()');
			}
		
		// ---- findBypassBoot()
		
		// ---- mostRecent()
			$product = Product::mostRecent();

			if(! (is_object($product)) ){
				error(__LINE__ .' mostRecent()');
			}

			$products = Product::mostRecent(5);

			if(! (count($products) === 5) ){
				error(__LINE__ .' mostRecent()');
			}

			if(! (is_array($products)) ){
				error(__LINE__ .' mostRecent()');
			}

		// ---- findOrFail()
			try {
				Product::findOrFail(9999999);
			} catch (Exception $e) {
				$caught = TRUE;
			}

			if(! (isset($caught)) ){
				error(__LINE__ .' findOrFail()');
			}
		
		// ---- all()
			if(! (is_array(Product::all())) ){
				error(__LINE__ .' findOrFail()');
			}

			if(! (count(Product::all(2)) == 2) ){
				error(__LINE__ .' findOrFail()');
			}
		
		// ---- asList()
			$products = Product::asList();

			if(! (is_array($products)) ){
				error(__LINE__ .' asList()');
			}

			if(! (is_object($products[array_keys($products)[0]])) ){
				error(__LINE__ .' asList()');
			}

			$products = Product::asList('color', Product::all());

			if(! (is_array($products)) ){
				error(__LINE__ .' asList()');
			}

			$products = Product::asList('color', [
				Product::insert([
					'color' => 'black'
				]),
				Product::insert([
					'color' => 'black'
				])
			]);

			if(! (is_array($products)) ){
				error(__LINE__ .' asList()');
			}

			if(! ($products[array_keys($products)[0]] === 'black') ){
				error(__LINE__ .' asList()');
			}

		
		// ---- finder()
			$products = Product::finder('heavy');

			if(! (is_array($products)) ){
				error(__LINE__ .' finder("heavy")');
			}

			if(! (is_object($products[0])) ){
				error(__LINE__ .' finder("heavy")');
			}

			// PostFinder
			$products = Product::finder('blue');

			if(! (is_array($products)) ){
				error(__LINE__ .' finder("blue")');
			}

			if(! ($products[0] === 'blue') ){
				error(__LINE__ .' finder("blue")');
			}

		// ---- where()
			$products = Product::where('color', 'blue');

			if(! (is_array($products)) ){
				error(__LINE__ .' where("color", "blue")');
			}

			if(! (is_object($products[0])) ){
				error(__LINE__ .' where("color", "blue")');
			}
			
			// complexWhere
			Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home', 'office']
			]);

			Product::insert([
				'title' => 'HIDDEN',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250',
			    'category' => ['home']
			]);

			$where = [
				'meta_relation' => 'AND',
				'tax_relation' => 'OR',
				[
					'meta_key' => 'color',
					'meta_value' => 'blue'
				],
				[
					'meta_key' => 'weight',
					'meta_value' => '250'
				],
				[
					'taxonomy' => 'category',
					'field' => 'slug',
					'operator' => 'OR',
					'terms' => ['home', 'office']
				],
			];

			$products = Product::where($where);
			
			// complexWhere + TAX
			
		// ---- in()
			$product = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product2 = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			if(! (count(Product::in($product->ID, $product->ID)) == 2) ){
				error(__LINE__ .' in()');
			}


	// -----------------------------------------------------
	// SAVE
	// -----------------------------------------------------
	
		// ---- save()
			$product = new Product([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product->addTaxonomy('category', 'home');

			$product->save();
			
			if(! (isset($product->ID)) ){
				error(__LINE__ .' save()');
			}
			
			if(! ($product->dirty === FALSE) ){
				error(__LINE__ .' save()');
			}

			$product2 = Product::find($product->ID);
			
			if(! ($product2->color === 'blue') ){
				error(__LINE__ .' save()');
			}
			
			if(! (count($product2->category) === 1) ){
				error(__LINE__ .' save()', $product2);
			}

		// ---- save models
			$savedProduct = Product::insert([
				'title' => 'product',
			]);

			$order = new Order;
			$order->saved_product = $savedProduct->ID;
			$order->product = new Product;
			$order->products = [
				$savedProduct,
				new Product,
				new Product,
			];

			$order->save();


			if(! (gettype($order->saved_product) === 'object') ){
				error(__LINE__ .' save()');
			}

			if(! ($order->saved_product instanceof WP_Model) ){
				error(__LINE__ .' save()');
			}

			if(! (gettype($order->product) === 'object') ){
				error(__LINE__ .' save()');
			}

			if(! ($order->product instanceof WP_Model) ){
				error(__LINE__ .' save()');
			}

			if(! ($order->product->new === FALSE) ){
				error(__LINE__ .' save()');
			}

			if(! (is_array($order->products)) ){
				error(__LINE__ .' save()');
			}

			if(! ($order->products[0] instanceof WP_Model) ){
				error(__LINE__ .' save()');
			}
		

	// -----------------------------------------------------
	// DELETE
	// -----------------------------------------------------
	
		// ---- delete()
			$product = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product->delete();
			
			if(! (get_post_status($product->ID) === 'trash') ){
				error(__LINE__ .' delete()');
			}

		// ---- hardDelete()
			$product = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$ID = $product->ID;

			$product->hardDelete();
			
			if(! ($product->color === NULL) ){
				error(__LINE__ .' hardDelete()');
			}

			$caught = FALSE;
			try {
				Product::findOrFail($ID);
			} catch (Exception $e) {
				$caught = TRUE;
			} finally {
				if(!$caught){
					error(__LINE__ .' hardDelete()');
				}
			}
		
		// ---- restore()
			$product = Product::insert([
				'title' => 'product',
				'content' => 'content',
			    'color' => 'blue',
			    'weight' => '250'
			]);

			$product->delete();
			
			if(! (get_post_status($product->ID) === 'trash') ){
				error(__LINE__ .' restore()');
			}

			$product2 = Product::restore($product->ID);
			
			if(! ($product2->color === 'blue') ){
				error(__LINE__ .' restore()');
			}
			
	
}


?>