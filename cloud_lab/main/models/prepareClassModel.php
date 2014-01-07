<?php
	/**
	*@param $charpter
	*@param $classId
	*@param $lesson
	*@return ppt和video的播放号序列,以及每页ppt老师的备注信息
	*/
	function getSnapAndRemarkOfPPT($classId,$charpter,$lesson)
	{
		$slides;
		$vedio;
		$remarkTemp;
		$remark;
		$sql = "SELECT Detail.PIC_URL AS url,Detail.VIDEO_SECTION AS section 
		FROM t_ppt_info Info, t_ppt_det Detail,t_class_info Class
		WHERE Class.CLASS_ID = "."$classId"." AND Class.COURSE_ID = Info.COURSE_ID 
		AND Detail.PPT_ID = Info.PPT_ID AND Info.COURSE_CHARPTER = "."$charpter".
		" AND Info.LESSON_SEQ = "."$lesson ORDER BY PPT_PAGE_NUM ASC";
		//echo $sql;
		$result = mysql_query($sql);
		$i = 0;
		while($row = mysql_fetch_array($result)){
			$slides[$i] = $row['url'];
			$vedio[$i++] = $row['section'];			
		}
		$sql = "SELECT Remark.PPT_PAGE_NUM AS page,Remark.REMARK AS remark 
		FROM t_ppt_remark Remark,t_ppt_info Info,t_class_info Class
		WHERE Class.CLASS_ID = "."$classId"." AND Class.COURSE_ID = Info.COURSE_ID 
		AND Remark.PPT_ID = Info.PPT_ID AND Info.COURSE_CHARPTER = "."$charpter".
		" AND Info.LESSON_SEQ = "."$lesson";
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)){
			$remarkTemp[$row['page']] = $row['remark'];		
		}

		for($i=0;$i<count($slides);$i++){
			if(array_key_exists($i+1,$remarkTemp))
				$remark[$i] = $remarkTemp[$i+1];
			else{
				$remark[$i] = null;
			}
		}

		$ret['snap'] = $slides;
		$ret['vedio'] = $vedio;
		$ret['remark'] = $remark;
		mysql_free_result($result);	
		return $ret;
	}


	/**
	*@param $classid 	当前课程的信息之班级
	*@param $charpterId 当前课程的信息之章节
	*@param $lessonId 	当前课程的信息之课时
	*@param $pagenum 	当前ppt 对应的页号
	*@param $remark     当前ppt的备注
	*@return 	true :save success false :save failed	
	*用来保存当前ppt的备注
	*/
	function savePPTRemarkPreClass($classId,$charpter,$lesson,$pagenum,$remark)
	{		
		$remark2 = iconv('UTF-8','gb2312//IGNORE',$remark);
		$sql = "SELECT Remark.PPT_ID AS PPT_ID FROM t_ppt_remark Remark,t_ppt_info Info,t_class_info Class 
		WHERE Class.CLASS_ID = "."$classId"." AND Class.COURSE_ID = Info.COURSE_ID 
		AND Remark.PPT_ID = Info.PPT_ID AND Info.COURSE_CHARPTER = "."$charpter".
		" AND Info.LESSON_SEQ = "."$lesson"." AND Remark.PPT_PAGE_NUM = "."$pagenum";
		mysql_query("set names 'gbk'");
		$result = mysql_query($sql);
		if(mysql_num_rows($result)<1){//没有记录要新插入一条记录			
			$sql = "SELECT Info.PPT_ID AS PPT_ID FROM t_ppt_info Info,t_class_info Class 
			WHERE Class.CLASS_ID = "."$classId"." AND Class.COURSE_ID = Info.COURSE_ID 
			 AND Info.COURSE_CHARPTER = "."$charpter".
			" AND Info.LESSON_SEQ = "."$lesson";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			$pptId = $row['PPT_ID'];
			$sql = "INSERT INTO t_ppt_remark (CLASS_ID,PPT_ID,PPT_PAGE_NUM,REMARK) 
			VALUES ($classId, $pptId, $pagenum, '$remark2')";			
		} else{//有记录只要更新就行
			$row = mysql_fetch_array($result);
			$pptId = $row['PPT_ID'];
			$sql = "UPDATE t_ppt_remark SET REMARK = "."'$remark2'".
			" WHERE PPT_ID = "."$pptId"." AND CLASS_ID = "."$classId".
			" AND PPT_PAGE_NUM = "."$pagenum";			
		}
		//echo $sql;
		$status = mysql_query($sql);
		return $status;
	}



	/**
	*@param $classid 	当前课程的信息之班级
	*@param $charpterId 当前课程的信息之章节
	*@param $lessonId 	当前课程的信息之课时
	*@return 	问题以及答案
	*用来获得相应课程的作业问题以及参考答案	
	*/
	function getRelatedQuestions($classId,$charpter,$lesson)
	{
		$question;
		$answer;
		$sql = "SELECT Q.QUESTION_CONTENT AS question, Q.ANSWER AS answer 
		FROM t_question_info Q, t_question_lesson_rel R, t_class_info C 
		WHERE C.CLASS_ID = $classId AND C.COURSE_ID = R.COURSE_ID 
		AND R.QUESTION_ID = Q.QUESTION_ID AND R.COURSE_CHARPTER = $charpter 
		AND R.LESSON_SEQ = $lesson";
		mysql_query("set names 'GB2312'");
		$result = mysql_query($sql);
		$i = 0;
		while($row = mysql_fetch_array($result)){
			$question[$i] = iconv('gb2312//IGNORE','UTF-8',$row['question']);
			$answer[$i++] = iconv('gb2312//IGNORE','UTF-8',$row['answer']);
		}
		$ret['question'] = $question;
		$ret['answer'] = $answer;
		return $ret;
	}

	/**
	 *@param $classid 	当前课程的信息之班级
	 *@param $charpterId 当前课程的信息之章节
	 *@param $lessonId 	当前课程的信息之课时
	 *@param $questionids 教师选中的问题号
	 *@return 	true OR false
	 *储存老师选中的问题
	*/
	function saveHomeworkList($classId,$charpterId,$lesson,$questionids,$deadline)
	{
		$status = false;
		$sql = "DELETE FROM t_homework_question_rel,
		WHERE HOMEWORK_ID IN (
			SELECT HOMEWORK_ID FROM t_homework_info  
			WHERE CLASS_ID = $classId AND COURSE_CHARPTER = $charpterId 
			AND LESSON_SEQ = $lesson)";
		//echo $sql;
		mysql_query($sql);
		$sql = "DELETE FROM t_homework_info WHERE CLASS_ID = $classId 
		AND COURSE_CHARPTER = $charpterId AND LESSON_SEQ = $lesson";
		mysql_query($sql);
		$sql = "INSERT INTO t_homework_info (CLASS_ID,COURSE_CHARPTER,LESSON_SEQ,DEADLINE) 
		VALUES ($classId,$charpterId,$lesson,'$deadline')";
		mysql_query($sql);
		//echo $sql;
		$homeworkId = mysql_insert_id();
		$questionNum = count($questionids);
		for($i = 0;$i<$questionNum;$i++){
			$questionId = $questionids[$i];
			$sql = "INSERT INTO t_homework_question_rel (HOMEWORK_ID, QUESTION_ID)
			VALUES ($homeworkId,$questionId)";
			//echo $sql;
			$status = mysql_query($sql);	
		}
		return true;
	}


	/**
	*@param $classid 	当前课程的信息之班级
	*@param $charpterId 当前课程的信息之章节
	*@param $lessonId 	当前课程的信息之课时
	*@param $preparation 本节课的备课信息
	*@return 	true :save success false :save failed	
	*用来保存当前课程的教案
	*/
	function savePreparationPreClass($classId,$charpter,$lesson,$preparation)
	{
		
	}
?>