/**
 * 
 * Since I developed this plugin for one of my project, by default Materialize components such as 
 * Toast, Modal, Button classes and FontAwesome icons, etc.. have been used throughout the plugin.
 * 
 * but feel free to customize the look to your needs.
 * 
 * Materialize css
 * https://materializecss.com/
 * 
 * FontAwesome 5.6.3
 * https://fontawesome.com/changelog/latest
 * 
 * WishList by Farhan Ullah
 * https://github.com/farhankk360/osclass-wishlist-plugin/
 * 
 */

jQuery(document).ready(function($) {

    //POST
    //add or delete items from wishlist (global)
    $(".wishlist").click(function() {
        let id = $(this).attr("id");
        let dataString = 'id='+ id ;
        let button = $(this);

        $.ajax({
            type: "POST",
            url: wishlist_url,
            data: dataString,
            cache: false,

            success: function(data) {
                console.log(data);
            // item added
            if(data.added){
                button.addClass('added');
                //fontaweosome icon
                button.html('<i class="fas fa-heart"></i>');

                //materializecss toast message
                M.toast({html: `"<strong>${data.item_title}</strong>" has been added to your wishlist.` , displayLength: 5000});
                
            }else if(data.removed) {    
                button.removeClass('added');
                //fontaweosome icon
                button.html('<i class="far fa-heart"></i>');

                //materializecss toast message
                M.toast({html: `"<strong>${data.item_title}</strong>" has been removed from your wishlist.` , displayLength: 5000});
            } else if(data.login_url) {
                 
                 M.toast({html: `<p>Please <a href=${data.login_url}>Login</a> to add "<strong>${data.item_title}</strong>" to your wishlist</p>` , displayLength: 5000});
            } else {

                M.toast({html: `${data.message}` , displayLength: 5000});
            }
          
            }
        });
    });

    //GET
    // get items from wishlist 
    $('.wishlist-items').click(function() {
        $.ajax({
            type: "GET",
            url: wishlist_url,

            success: function(data){
                console.log(data);
                if(data.success) {                
                    //materialize collection list view
                    let ul = ['<h5>Your Wishlist</h5><ul class="collection">'];
                    data.data.forEach(ele => {
                        //OPTIONAL comma separate price format method, from my helper functions for more info
                        //Gist: https://gist.github.com/farhankk360/236eca9ee0a76500e561ff85883db395
                        let price = ele.item_price.split(' ');
                        price[1] = handleFormat(price[1]);

                        let li = `<li class="collection-item avatar">
                        <a href="${ele.item_url}"><img src="${ele.item_thumb}" alt="${ele.item_title}" class="circle"></a>
                        <a href="${ele.item_url}"><span class="title">${ele.item_title}</span></a>
                        <p class="red-text darken-4"><strong>${price.join(' ')}</strong></p>
                        <a id="${ele.item_id}" href="javascript://" class="wishlist-delete secondary-content red-text darken-4" title="Remove item from your wishlist"><i class="fa fa-times"></i></a>
                        </li>`
                        ul.push(li);
                    });
                    ul.push('</ul>');
                    
                    $('#wishlist-modal').find('.modal-content').html(ul.join(''));
                    
                    //init wishlist delete button
                    wishListDelete();
                } else {
                    $('#wishlist-modal').find('.modal-content').html('<h5>Your Wishlist</h5><p>There are no items in your wishlist!</p>');
                }
            }

        })
    });

    //POST
    //delete items from wishlist (modal-view)
    function wishListDelete(){
        $('.wishlist-delete').click(function(){
            let id = $(this).attr("id");
            let dataString = 'id='+ id ;
            let button = $(this);

            $.ajax({
                type: "POST",
                url: wishlist_url,
                data: dataString,
                cache: false,

                success: function(data){
                    if(data.success) {
                        if($('#wishlist-modal').find('ul li').size() > 1){
                            button.parent().remove();
                            //materializecss toast message
                            M.toast({html: `"<strong>${data.item_title}</strong>" has been removed from your wishlist.` , displayLength: 5000});
                        } else {
                            $('#wishlist-modal').find('.modal-content').html('<h5>Your Wishlist</h5><p>There are no items in your wishlist!</p>'); 
                        }
                        
                    }
                }
            })
            
        })
    }

    //Helper function
    //handle comma seperated prices (global function)
   function handleFormat(num) {
    var decimal = '';
    if (num.includes('.')) {

        decimal = num.split('.');
        num = decimal[0];
    }
    if (num.length > 3) {
        for (let i = num.length - 3; i > 0; i -= 3) {
            num = num.substr(0, i) + ',' + num.substr(i);
        }
    }
    if (decimal !== '') {
        return num + '.' + decimal[1];
    }
    return num;
} 
    
});