    <div class="container top">

      <ul class="breadcrumb">
        <li>
          <a href="<?php echo site_url("admin"); ?>">
            <?php echo ucfirst($this->uri->segment(1));?>
          </a> 
          <span class="divider">/</span>
        </li>
        <li class="active">
          Show Inventory
        </li>
      </ul>

      <div class="page-header users-header">
        <h2>
            Show Inventory
          <a  href="<?php echo site_url("admin").'/'.$this->uri->segment(2); ?>/addinventory" class="btn btn-success">Add a new</a>
        </h2>
      </div>
      
      <div class="row">
        <div class="span12 columns">
          <div class="well">
           
            <?php
           
            $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
           
            $options_manufacture = array(0 => "all");
//            foreach ($manufactures as $row)
//            {
//              $options_manufacture[$row['id']] = $row['name'];
//            }
            //save the columns names in a array that we will use as filter         
            $options_products = array();    
//            foreach ($products as $array) {
//              foreach ($array as $key => $value) {
//                $options_products[$key] = $key;
//              }
//              break;
//            }

              echo form_open('admin/expense', $attributes);
              echo form_label('Search:', 'search_string');
              echo form_input('search_string', $search_string_selected, 'style="width: 170px;height: 26px;"');
              $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

              
              echo form_submit($data_submit);

            echo form_close();
            ?>

          </div>

          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th class="header">#</th>
                <th class="yellow header headerSortDown">House No.</th>
                <th class="green header">Items</th>
                <th class="green header">Make</th>
                <th class="green header">Model</th>
                <th class="green header">Purchase Date</th>
                <th class="green header">Purchase Amount</th>
                <th class="green header"> Vendor Name</th>
                <th class="green header">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
             // $arr_exp_or_amc = array(''=>'--Select--' ,1=>'Annual Expense', 2=>'AMC' ); 
              foreach($inventory as $row)
              {

                echo '<tr>';
                echo '<td>'.$row['inv_id'].'</td>';
                echo '<td>'.$row['house_no'].'</td>';
                echo '<td>'.$inventory_items[$row['inv_item']].'</td>';
                echo '<td>'.$row['make'].'</td>';
                echo '<td>'.$row['model'].'</td>';
                echo '<td>'.$row['purchase_date'].'</td>';
                echo '<td>'.$row['purchase_amount'].'</td>';
                echo '<td>'.$row['vendor_name'].'</td>';
             
                echo '<td class="crud-actions">
                  <a href="'.site_url("admin").'/expense/updateinventory/'.$row['inv_id'].'" class="btn btn-info">view & edit</a>  
                  <a href="'.site_url("admin").'/expense/deleteinventory/'.$row['inv_id'].'" class="btn btn-danger">delete</a>
                </td>';
                echo '</tr>';
              }
              ?>      
            </tbody>
          </table>

          <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>

      </div>
    </div>