<?php

/**
 * Return data type = JSON;
 */

    //post request -> works like a toggle, e.g if an item exists in user's wishlist, items is removed, if not then its added.
    //else user is not logged in.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && Params::getParam('id') != '') {
        $id    = Params::getParam('id');

         // get current item details
         View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey($id));

        //error user is not logged in
        if( ( osc_logged_user_id() == osc_item_user_id() ) && osc_logged_user_id() != 0 ) { 
            
            header("Content-Type: application/json");
            $result = array(
                'success' => false,
                'message' => "It's your own ad, you can't add it to your wishlist."
            );
            echo json_encode($result);

            return;
        }

        //check if user is logged in
        if ( osc_is_web_user_logged_in() ) {
            //check if the item is not already in the watchlist
            $db   = getConnection();
            $data = $db->osc_dbFetchResult("SELECT * FROM %st_item_wishlist WHERE fk_i_item_id = %d and fk_i_user_id = %d", DB_TABLE_PREFIX, $id, osc_logged_user_id());

            //If nothing returned then proceed with adding item to wishlist
            if (!isset($data['fk_i_item_id'])) {
                $db = getConnection();
                $db->osc_dbExec("INSERT INTO %st_item_wishlist (fk_i_item_id, fk_i_user_id) VALUES (%d, '%d')", DB_TABLE_PREFIX, $id, osc_logged_user_id());
                
                //return json response
                header("Content-Type: application/json");
                $result = array(
                    'success' => true,
                    'item_id' => $id,
                    'item_title' => osc_item_title(),
                    'added' => true
                );
                echo json_encode($result);
                
            } else {
                //already in wishlist, delete the item!
                // delete item from watchlist
                $db = getConnection();
                $result = $db->osc_dbExec("DELETE FROM %st_item_wishlist WHERE fk_i_item_id = %d AND fk_i_user_id = %d LIMIT 1", DB_TABLE_PREFIX , $id, osc_logged_user_id());
                
                //return json response
                header("Content-Type: application/json");
                $result = array(
                    'success' => true,
                    'item_id' => $id,
                    'item_title' => osc_item_title(),
                    'removed' => true
                );
                echo json_encode($result);
            }
        } else {
            //error user is not logged in
            header("Content-Type: application/json");
            $result = array(
                'success' => false,
                'item_title' => osc_item_title(),
                'login_url' => osc_user_login_url()
            );
            echo json_encode($result);

        }
        //get request -> returns all wishlist items for the user
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        
        if(osc_is_web_user_logged_in()) {
            $user_id = osc_logged_user_id();


            //Search Instance
            Search::newInstance()->addConditions(sprintf("%st_item_wishlist.fk_i_user_id = %d", DB_TABLE_PREFIX, $user_id));
            Search::newInstance()->addConditions(sprintf("%st_item_wishlist.fk_i_item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_wishlist", DB_TABLE_PREFIX));
        // Search::newInstance()->page($iPage, $itemsPerPage);

        $aItems = Search::newInstance()->doSearch();
        View::newInstance()->_exportVariableToView('items', $aItems);

        $result = array();
        $i = 0;

        if(osc_count_items() == 0){
            //no items in wishlist
            header("Content-Type: application/json");
            $result = array(
                'success' => false,
                'message' => 'your wishlish is empty!'
            );
            echo json_encode($result);

            } else {
            
            $result['success'] = true;

         while ( osc_has_items() ) { 
                        $result['data'][$i]['item_id'] = osc_item_id(); 
                 if (osc_images_enabled_at_items()) { 
                   
                      if (osc_count_item_resources()) {
                        $result['data'][$i]['item_thumb'] = osc_resource_thumbnail_url();
                    } else {
                        $result['data'][$i]['item_thumb'] = osc_current_web_theme_url('images/no_photo.gif');
                     }   
                 }
                        $result['data'][$i]['item_title'] = osc_item_title();
                   
                        $result['data'][$i]['item_url'] = osc_item_url();

                        if (osc_price_enabled_at_items()) { 
                            $result['data'][$i]['item_price'] = osc_item_formated_price(); 
                        } 
                        $result['data'][$i]['item_city'] = osc_item_city(); 
                        $result['data'][$i]['item_region'] = osc_item_region(); 
                        $result['data'][$i]['item_pub_date'] = osc_format_date(osc_item_pub_date());
                        $result['data'][$i]['item_description'] = osc_highlight(strip_tags(osc_item_description()));

                        $i++;
            }

            //return wishlist items
            header("Content-Type: application/json");
            echo json_encode($result);
            
        }
        
        } else {
            //error user is not logged in
            header("Content-Type: application/json");
            $result = array(
                'success' => false,
                'message' => 'user is not logged in!'
            );
            echo json_encode($result);
        }
        
    }


?>