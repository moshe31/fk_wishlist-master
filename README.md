## WishList
free plugin for [osclass](https://osclass.org/)  php based platform which allows you to quickly create and manage your own free classifieds site.
___
#### Description
WishList allows users to add items / ads to there wishlist to keep track of them.
heart button works like a toggle, if the item is already in your wish list, its removed, if not then its added, also changing the color state of the heart shape.

To add or remove items from wishlist
Make a `POST` request with body `id={item_id}` to this endpoint.
`http://{your-domain}/index.php?page=ajax&action=custom&ajaxfile=fk_wishlist/ajax_wishlist.php;`

returns JSON response, confirming if the following item as been added or removed or there has been an error.
```
{
   "success":true,
   "item_id":"7",
   "item_title":"Honda cg 125",
   "added":true
}
```

furthurmore you can make a `GET` request to get all your wishlist items, from this end point via AJAX;
`http://{your-domain}/index.php?page=ajax&action=custom&ajaxfile=fk_wishlist/ajax_wishlist.php;`

it return JSON data in the following format:
```
{
   "success":true,
   "data":[
      {
         "item_id":7,
         "item_thumb":"http://localhost/pakisell/oc-content/uploads/0/19_thumbnail.jpg",
         "item_title":"Honda cg 125",
         "item_url":"http://localhost/pakisell/vehicles/motorcycles/honda-cg-125_i7",
         "item_price":"Rs 75000",
         "item_city":"Dera Ghazi Khan",
         "item_region":"Punjab",
         "item_pub_date":"January 7, 2019",
         "item_description":"Honda cg 125 2013 model ha very good condition ha, long time bilkul use nai kia ar nb rawlpindi ka 2014 ka lga hoa ha full jenun ha urgen for sale need mony"
      },
      ...
   ]
}
```
so you can get creative and make your own views, or use bootstrap modals
___
Since I developed this plugin for one of my project, by default Materialize components such as Toast, Modal, Button classes and FontAwesome icons, etc.. have been used throughout the plugin.
  
  but feel free to customize the look to your needs.
  
  Materialize css
  https://materializecss.com/
  
  FontAwesome 5.6.3
  https://fontawesome.com/changelog/latest
 

##### Examples: 
item added to wishlist

![screenshot](https://www.dropbox.com/s/ad36b5ovz6b9dl6/wishlist-1.jpg?raw=1)
___
item removed from wishlist

![screenshot](https://www.dropbox.com/s/pz6x6hgx7h26wu3/wishlist-2.jpg?raw=1)
___
View items in your wishlist

![screenshot](https://www.dropbox.com/s/0co9swv6g7jhwt8/wishlist-3.jpg?raw=1)

___
#### Installation
Simply clone the repo, copy fk_wishlist folder to your osclass  plugins folder
```
Path:
{your-path}/oc-content/plugins
```
go to admin panel -> manage plugins -> there find WishList -> click on install.
___
#### Usage

add the following code in `loop-single.php` and `loop-single-premium.php`

what below method basically does, it checks weather current item in the loop iteration is added to user's wishlist, if its added then it returns an `<a>` tag with a class `wishlist added` if not then it only return `<a>` with just `wishlist` class.
```
   <!-- wishlist plugin heart button -->
 <?php wishlist_item_check(osc_item_id())?>
```