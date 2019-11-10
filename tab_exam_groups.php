<?php
@session_start();

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit ;
}

// if (isset($_SESSION['level'])) {
// 	if ($_SESSION['level'] < 5)
// 	{
// 		die("premium feature");
// 		return;
// 	}
// } else {
// 	die("relogin please");
// 	return;	
// }
?>

<script>

	$(function ()
		{
					//http://wenzhixin.net.cn/p/bootstrap-table/docs/examples.html#via-javascript-table
					$('#exam_groups_tbl').bootstrapTable();
					
										//new record
										$('#btn_exam_groups_new').on('click', function(e)
										{
											$('#lblTitle_EXAM_GROUPS').html("New EXAM_GROUPS");
											
											$('#modalEXAM_GROUPS').modal('toggle');
										});
											
										//edit record
										$('#btn_exam_groups_edit').on('click', function(e)
										{
											var row = $('#exam_groups_tbl').bootstrapTable('getSelections');
					
											if (row.length>0)
												{
													query_EXAM_GROUPS_modal(row[0].examgrp_id);
												}
											else 
												alert("Please select a row");
										});
										
										//delete record
										$('#btn_exam_groups_delete').on('click', function(e)
										{
											var row = $('#exam_groups_tbl').bootstrapTable('getSelections');
					
											if (row.length>0)
												{
													if (confirm("Would you like to delete " + row[0].examgrp_name + " ?"))
														delete_EXAM_GROUPS(row[0].examgrp_id);
												}
											else 
												alert("Please select a row");
										});
										
					
										////////////////////////////////////////
										// MODAL FUNCTIONALITIES [START]
										//when modal closed, hide the warning messages + reset
										$('#modalEXAM_GROUPS').on('hidden.bs.modal', function() {
											//when close - clear elements
											$('#formEXAM_GROUPS').trigger("reset");
									 
											//clear validator error on form
											//validatorEXAM_GROUPS.resetForm();
										});
									 
										//functionality when the modal already shown and its long, when reloaded scroll to top
										$('#modalEXAM_GROUPS').on('shown.bs.modal', function() {
											$(this).animate({
												scrollTop : 0
											}, 'slow');
										});
										// MODAL FUNCTIONALITIES [END]
										////////////////////////////////////////
										
			
										
										////////////////////////////////////////
										// MODAL SUBMIT aka save & update button
										$('#formEXAM_GROUPS').submit(function(e) {
											e.preventDefault();
										 
											////////////////////////// validation
											var form = $(this);
											// form.validate();
										 
											// if (!form.valid())
											// 	return;
											////////////////////////// validation
										 
											var postData = $(this).serializeArray();
											var formURL = $(this).attr("action");
										 
											 loading.appendTo($('#formEXAM_GROUPS'));
					
											//close modal
											//$('#modalEXAM_GROUPS').modal('toggle');
										 
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
														$('#exam_groups_tbl').bootstrapTable('refresh');
					
														//close modal
														$('#modalEXAM_GROUPS').modal('toggle');
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
									function queryParamsEXAM_GROUPS(params)
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
									function query_EXAM_GROUPS_modal(rec_id){
										loading.appendTo(document.body);
										
										$.ajax(
										{
											url : "tab_exam_groups_fetch.php",
											type: "POST",
											data : { examgrp_id : rec_id },
											success:function(data, textStatus, jqXHR)
											{
												loading.remove();
												
												if (data!='null')
												{
													 $("[name=exam_groupsFORM_updateID]").val(data.examgrp_id);
							$('[name=examgrp_name]').val(data.examgrp_name);
					
													 
													 $('#lblTitle_EXAM_GROUPS').html("Edit EXAM_GROUPS");
													$('#modalEXAM_GROUPS').modal('toggle');
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
									function delete_EXAM_GROUPS(rec_id){
										loading.appendTo(document.body);
										
										$.ajax(
										{
											url : "tab_exam_groups_delete.php",
											type: "POST",
											data : { examgrp_id : rec_id },
											success:function(data, textStatus, jqXHR)
											{
												loading.remove();
												
												if (data=='00000')
												{
													//refresh
													$('#exam_groups_tbl').bootstrapTable('refresh');
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
		
		
			
</script>


<div class="container">

<br>

<button id="btn_exam_groups_new" type="button" class="btn btn-success">
New
</button>
<button id="btn_exam_groups_edit" type="button" class="btn btn-primary">
Edit
</button>
<button id="btn_exam_groups_delete" type="button" class="btn btn-danger">
Delete
</button> 
	<br><br>
	
	<table id="exam_groups_tbl"
			data-toggle="table"
			data-striped=true
			data-url="tab_exam_groups_pagination.php"
			data-show-columns="false"
			data-search="false"
			data-show-refresh="false"
			data-show-toggle="false"
			data-pagination="true"
			data-click-to-select="true" data-single-select="true"
			data-page-size="10"
			data-height="480"
			data-side-pagination="server"
			data-query-params="queryParamsEXAM_GROUPS">

		<thead>
			<tr>
				<th data-field="state" data-checkbox="true" >
				</th>

				<th data-field="examgrp_id" data-visible="false">
					id
				</th>
				
				<th data-field="examgrp_name" data-sortable="true">
					Group name
				</th>				
			</tr>
		</thead>
	</table	>
</div>

<!-- NEW EXAM_GROUPS MODAL [START] -->
<div class="modal fade" id="modalEXAM_GROUPS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" 	 aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id='lblTitle_EXAM_GROUPS'>New</h4>
			</div>
			<div class="modal-body">
				<form id="formEXAM_GROUPS" role="form" method="post" action="tab_exam_groups_save.php">

			
				<div class='form-group'>
					<label>Name :</label>
					<input name='examgrp_name' class='form-control' maxlength="9999" placeholder='group name' required>
				</div>
			</div>



						<!-- <input name="exam_groupsFORM_FKid" id="EXAM_GROUPS_FKid" class="form-control" style="display:none;"> -->
						<input name="exam_groupsFORM_updateID" id="exam_groupsFORM_updateID" class="form-control" style="display:none;">

						<div class="modal-footer">
							<button id="bntCancel_EXAM_GROUPS" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_EXAM_GROUPS" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
                </form>
            </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
    </div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW EXAM_GROUPS MODAL [END] -->