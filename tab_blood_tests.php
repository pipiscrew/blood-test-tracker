<?php
@session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}

?>

		<script>
			var blood_test_id_only_for_fill_grid = 0;

			$(function ()
				{


					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#blood_tests_tbl').bootstrapTable();

					$('#blood_tests_tbl').on('click-row.bs.table', function (e, row, $element) {
						
						$('#selected_blood_test').html(row.blood_date + " (" + row.blood_where + ")");
						
						//set select blood test ID to a public variable
						blood_test_id_only_for_fill_grid = row.blood_id;

						//force child table to refresh (in query params using the blood_test_id_only_for_fill_grid)
						$('#blood_test_exams_tbl').bootstrapTable('refresh');
							//console.log($element);
					});

					//new record
					$('#btn_blood_tests_new').on('click', function(e)
					{
						$('#lblTitle_BLOOD_TESTS').html("New BLOOD_TESTS");
						
						$('#modalBLOOD_TESTS').modal('toggle');
					});
						
					//edit record
					$('#btn_blood_tests_edit').on('click', function(e)
					{
						var row = $('#blood_tests_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								query_BLOOD_TESTS_modal(row[0].blood_id);
							}
						else 
							alert("Please select a row");
					});
					
					//delete record
					$('#btn_blood_tests_delete').on('click', function(e)
					{
						var row = $('#blood_tests_tbl').bootstrapTable('getSelections');

						if (row.length>0)
							{
								if (confirm("Would you like to delete " + row[0].blood_date + " ?"))
									delete_BLOOD_TESTS(row[0].blood_id);
							}
						else 
							alert("Please select a row");
					});
					

				    ////////////////////////////////////////
				    // MODAL FUNCTIONALITIES [START]
				    //when modal closed, hide the warning messages + reset
				    $('#modalBLOOD_TESTS').on('hidden.bs.modal', function() {
				        //when close - clear elements
				        $('#formBLOOD_TESTS').trigger("reset");
				    });
				 
				    //functionality when the modal already shown and its long, when reloaded scroll to top
				    $('#modalBLOOD_TESTS').on('shown.bs.modal', function() {
				        $(this).animate({
				            scrollTop : 0
				        }, 'slow');
				    });
				    // MODAL FUNCTIONALITIES [END]
				    ////////////////////////////////////////
				    
				    
					////////////////////////////////////////
					// MODAL SUBMIT aka save & update button
					$('#formBLOOD_TESTS').submit(function(e) {
					    e.preventDefault();
					 
					    ////////////////////////// validation
					    var form = $(this);
					 
					    ////////////////////////// validation
					 
					    var postData = $(this).serializeArray();
					    var formURL = $(this).attr("action");
					 
					 	loading.appendTo($('#formBLOOD_TESTS'));

					    //close modal
					    //$('#modalBLOOD_TESTS').modal('toggle');
					 
					    $.ajax(
					    {
					        url : formURL,
					        type: "POST",
					        data : postData,
					        success:function(data, textStatus, jqXHR)
					        {
								loading.remove();

					            if (data=="00000")
								{	//refresh
									$('#blood_tests_tbl').bootstrapTable('refresh');

								    //close modal
					    			$('#modalBLOOD_TESTS').modal('toggle');
								}
					            else
					                alert("ERROR");
					        },
					        error: function(jqXHR, textStatus, errorThrown)
					        {
					        	loading.remove();
					            alert("ERROR - connection error");
					        }
					    });
					});

				}); //jQ ends
				
				//bootstrap-table
				function queryParamsBLOOD_TESTS(params)
				{
					var q = {
						"limit": params.limit,
						"offset": params.offset,
						"search": params.search,
						"name": params.sort,
						"order": params.order
					};
 
					return q;
				}
				
				//edit button - read record
				function query_BLOOD_TESTS_modal(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_blood_tests_fetch.php",
				        type: "POST",
				        data : { blood_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
							loading.remove();
							
				        	if (data!='null')
							{
							 	$("[name=blood_testsFORM_updateID]").val(data.blood_id);
								$('[name=blood_date]').val(data.blood_date);
								$('[name=blood_where]').val(data.blood_where);
								$('[name=blood_comments]').val(data.blood_comments);

							 	
							 	$('#lblTitle_BLOOD_TESTS').html("Edit BLOOD_TESTS");
								$('#modalBLOOD_TESTS').modal('toggle');
							}
							else
								alert("ERROR - Cant read the record.");
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
				//delete button - delete record
				function delete_BLOOD_TESTS(rec_id){
					loading.appendTo(document.body);
					
				    $.ajax(
				    {
				        url : "tab_blood_tests_delete.php",
				        type: "POST",
				        data : { blood_id : rec_id },
				        success:function(data, textStatus, jqXHR)
				        {
				        	loading.remove();
				        	
				        	if (data=='00000')
							{
								//refresh
								$('#blood_tests_tbl').bootstrapTable('refresh');
							}
							else
								alert("ERROR - Cant delete the record.");
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				        	loading.remove();
				            alert("ERROR");
				        }
				    });
				}
				
				function redirect_select(){
					var win = window.open('visualize_select.php', '_blank');
  					win.focus();
				}
					
		</script>

	</head>
	<body>
		<!-- <div class="container"> -->
		<br>
			<button id="btn_blood_tests_new" type="button" class="btn btn-success">
				New
			</button>
			<button id="btn_blood_tests_edit" type="button" class="btn btn-primary">
				Edit
			</button>
			<button id="btn_blood_tests_delete" type="button" class="btn btn-danger">
				Delete
			</button> 
			<button onclick="redirect_select()"  type="button" class="btn btn-warning">
				Visualize
			</button> 
			<br><br>
			<table id="blood_tests_tbl"
	           data-toggle="table"
	           data-striped=true
	           data-url="tab_blood_tests_pagination.php"
			   data-show-columns="false"
			   data-search="false"
			   data-show-refresh="false"
			   data-show-toggle="false"
	           data-pagination="true"
	           data-click-to-select="true" data-single-select="true"
	           data-page-size="10"
	           data-height="480"
	           data-side-pagination="server"
			   data-sort-name="blood_date"
			   data-sort-order="desc"
	           data-query-params="queryParamsBLOOD_TESTS">

				<thead>
					<tr>
						<th data-field="state" data-checkbox="true" >
						</th>

						<th data-field="blood_id" data-visible="false">
							blood_id
						</th>
						
						<th data-field="blood_date" data-sortable="true">
							Date
						</th>
						
						<th data-field="blood_where" data-sortable="true">
							Doctor
						</th>
						
						<th data-field="blood_comments" data-sortable="true">
							Comments
						</th>
						
					</tr>
				</thead>
			</table	>
		<!-- </div> -->



<!-- NEW BLOOD_TESTS MODAL [START] -->
<div class="modal fade" id="modalBLOOD_TESTS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_BLOOD_TESTS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formBLOOD_TESTS" role="form" method="post" action="tab_blood_tests_save.php">

			
				<div class='form-group'>
					<label>blood_date :</label>
					<input name='blood_date' class='form-control' maxlength="9999" placeholder='yyyy-mm-dd' required>
				</div>
			


			
				<div class='form-group'>
					<label>blood_where :</label>
					<input name='blood_where' class='form-control' maxlength="9999" placeholder='blood_where' required>
				</div>
			


			
				<div class='form-group'>
					<label>blood_comments :</label>
					<input name='blood_comments' class='form-control' maxlength="9999" placeholder='blood_comments'>
				</div>
			



						<!-- <input name="blood_testsFORM_FKid" id="BLOOD_TESTS_FKid" class="form-control" style="display:none;"> -->
						<input name="blood_testsFORM_updateID" id="blood_testsFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_BLOOD_TESTS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_BLOOD_TESTS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW BLOOD_TESTS MODAL [END] -->
