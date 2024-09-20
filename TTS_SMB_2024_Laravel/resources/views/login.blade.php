
  <form action="/login" method="POST">
    <div> 
    
        <input type="email" name="email">
        <input type="password" name="password">
        <input type="hidden" name="_token" value="<?php echo csrf_token();  ?> ">

    </div>
 
  <button type="submit">Login</button>


  </form>
  
