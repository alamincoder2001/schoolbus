	<div class="form-horizontal">

	    <div class="form-group">
	        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Job type </label>
	        <label class="col-sm-1 control-label no-padding-right">:</label>
	        <div class="col-sm-8">
	            <input type="text" id="job_type" name="job_type" placeholder="Job type"
	                value="<?php echo $selected->Job_Type; ?>" class="col-xs-10 col-sm-4" />
	            <input name="id" type="hidden" id="id" value="<?php echo $selected->jobType_SlNo; ?>" />
	            <span id="msg"></span>
	            <?php echo form_error('job_type'); ?>
	            <span style="color:red;font-size:15px;">
	        </div>
	    </div>

	    <div class="form-group">
	        <label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
	        <label class="col-sm-1 control-label no-padding-right"></label>
	        <div class="col-sm-8">
	            <button type="button" class="btn btn-sm btn-success" onclick="submited()" name="btnSubmit">
	                Submit
	                <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
	            </button>
	        </div>
	    </div>

	</div>