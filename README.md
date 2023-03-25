# basta-smoelenboek

This is a wordpress plugin that provides shortcodes for rendering a face book of the choir member of the Vocal Group BASTA!

To use, add a shortcode like 

```
[smoelenboek-grid selectfield='role-in-choir' selectvalues='soprano,alto,tenor,bass']
```

to a page. In this example, the field `role-in-choir` could be added to your wordpress users via a plugin like `Advanced Custom Fields`.  
Users matching the selectvalues specified in the selectfield specified are queried in the fields `meta_value` and `meta_key` respectively of the table `usermeta`.  

The shortcode generates HTML like:  
```html
<div class="smoelenboek">
    <div class="user">
        <div class="avatar">
            <img alt="avatar" src="http://0.gravatar.com/avatar/6b6496ea2ad2a27307b0fd25514d401a?s=96&amp;d=mm&amp;r=g">
        </div>
        <div class="name">User name</div>
        <div class="bio">Story</div>
    </div>
    <div class="user">
    ...
    </div>
</div>
```
