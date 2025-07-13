/* ------------- Global variables and methods ------------- */

var
  // Date Formats
  fmtDDMMYYYY   = 0, // '15/04/2004'
  fmtDDMMYYYY12 = 1, // '15/04/2004 08:10PM'
  fmtDDMMYYYY24 = 2, // '15/04/2004 20:10'
  fmtMMDDYYYY   = 3, // '04/15/2004'
  fmtMMDDYYYY12 = 4, // '04/15/2004 08:10PM'
  fmtMMDDYYYY24 = 5, // '04/15/2004 20:10'
  fmtYYYYMMDD24 = 6,
  // Resource Strings
  objEnglishStrings = {
    DayNames: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    InvalidDateError: '"%1" date is invalid',
    MonthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    NextMonthHint: 'Next month',
    OutOfYearRangeError: 'Date must be within the range from %1 to %2 year',
    PriorMonthHint: 'Prior month',
    SelectDateHint: 'Select a date',
    SelectHoursHint: 'Select hours',
    SelectMinutesHint: 'Select minutes',
    SelectMonthHint: 'Select a month',
    SelectYearHint: 'Select a year',
    TimeCaption: 'Time',
    TodayCaption: 'Today',
    WrongDateFormatError: 'Calendar doesn\'t support specified date format'
  },
  objRussianStrings = {
    DayNames: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    InvalidDateError: 'Дата "%1" имеет неверный формат',
    MonthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    NextMonthHint: 'Следующий месяц',
    OutOfYearRangeError: 'Дата должна быть в диапазоне с %1 по %2 год',
    PriorMonthHint: 'Предыдущий месяц',
    SelectDateHint: 'Выбрать дату',
    SelectHoursHint: 'Выбрать часы',
    SelectMinutesHint: 'Выбрать минуты',
    SelectMonthHint: 'Выбрать месяц',
    SelectYearHint: 'Выбрать год',
    TimeCaption: 'Время',
    TodayCaption: 'Сегодня',
    WrongDateFormatError: 'Календарь не поддерживает указанный формат даты'
  },
  objUkrainianStrings = {
    DayNames: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    InvalidDateError: 'Дата "%1" має невiрний формат',
    MonthNames: ['Сiчень', 'Лютий', 'Березень', 'Квiтень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
    NextMonthHint: 'Наступний мiсяць',
    OutOfYearRangeError: 'Дата повинна бути у дiапазонi з %1 по %2 рiк',
    PriorMonthHint: 'Попереднiй мiсяць',
    SelectDateHint: 'Вибрати дату',
    SelectHoursHint: 'Вибрати години',
    SelectMinutesHint: 'Вибрати хвилини',
    SelectMonthHint: 'Вибрати мiсяць',
    SelectYearHint: 'Вибрати рiк',
    TimeCaption: 'Час',
    TodayCaption: 'Сьогоднi',
    WrongDateFormatError: 'Календар не пiдтримує вказаний формат дати'
  },
  // Other
  arrCalendars = [],
  arrYearOffsets = [-50, -20, -10, -3, -2, -1, 0, 1, 2, 3, 10, 20, 50],
  intTimeoutID = 0,
  blnFrameInitialized = false,
  strListItemEvents = ' onmouseover="parent.AddElementClass(this, \'clsCalendarOverListItem\')" ' +
    'onmouseout="parent.RemoveElementClass(this, \'clsCalendarOverListItem\')"';

try {
  var strCalendarScriptPath = GetFilePath(document.getElementById('objCalendarScript').src);
  document.write('<link href="' + strCalendarScriptPath + 'calendar.css" type="text/css" rel=stylesheet>');
  document.write('<iframe id=objCalendarFrame name=CalendarFrame class=clsCalendarFrame ' +
    'frameborder=no scrolling=no tabindex="-1"></iframe>');
} catch(e) {
  alert('objCalendarScript object is not found');
}

function AddElementClass(objElement, strClassName) {
  objElement.className += ' ' + strClassName;
}

function Calendar(strDateObjectID, intDateFormat, strDateSeparator, strTimeSeparator,
   intYearFrom, intYearTo, blnWeekFromMonday, objResourceStrings) {
  return(new CalendarClass(strDateObjectID, intDateFormat, strDateSeparator, strTimeSeparator,
    intYearFrom, intYearTo, blnWeekFromMonday, objResourceStrings));
}

function GetElementPos(objElement) {
  var objResult = {x: objElement.offsetLeft, y: objElement.offsetTop};
  if (objElement.offsetParent && (objElement.style.position != 'absolute')) {
    var objTmp = GetElementPos(objElement.offsetParent);
    objResult.x += objTmp.x;
    objResult.y += objTmp.y;
  }
  return(objResult);
}

function GetFilePath(strFile) {
  var strResult = '';
  var arrFolders = strFile.split('/');
  for (var i = 0; i < arrFolders.length - 1; i++)
    strResult += arrFolders[i] + '/';
  return(strResult);
}

function HideCalendar() {
  document.getElementById('objCalendarFrame').style.top = '-500px';
  if (document.detachEvent)
    document.detachEvent('onmousedown', HideCalendar);
  else if (document.removeEventListener)
    document.removeEventListener('mousedown', HideCalendar, true);
  else
    document.onmousedown = null;
}

function IntToStr(intValue) {
  var strValue = intValue.toString();
  return(strValue.length == 1 ? '0' + strValue : strValue);
}

function RemoveElementClass(objElement, strClassName) {
  var arrClasses = objElement.className.split(' ');
  var strClasses = '';
  for (var i = 0; i < arrClasses.length; i++)
    if (arrClasses[i] != strClassName)
      strClasses += arrClasses[i] + ' ';
  objElement.className = strClasses.substr(0, strClasses.length - 1);
}

function ResizeCalendar() {
  var objTable = CalendarFrame.document.getElementById('objTable');
  var objFrameStyle = document.getElementById('objCalendarFrame').style;
  objFrameStyle.width = objTable.offsetWidth + 'px';
  objFrameStyle.height = objTable.offsetHeight + 'px';
}

function SameDate(objDate1, objDate2) {
  return((objDate1.getFullYear() == objDate2.getFullYear()) &&
    (objDate1.getMonth() == objDate2.getMonth()) && (objDate1.getDate() == objDate2.getDate()));
}

/* ------------- Calendar class ------------- */

function CalendarClass(strDateObjectID, intDateFormat, strDateSeparator, strTimeSeparator,
   intYearFrom, intYearTo, blnWeekFromMonday, objResourceStrings) {
  this.ID = arrCalendars.length;
  this.DateFormat = intDateFormat != null ? intDateFormat : fmtMMDDYYYY;
  this.DateObjectID = strDateObjectID;
  this.DateSeparator = strDateSeparator != null ? strDateSeparator : '/';
  this.TimeSeparator = strTimeSeparator != null ? strTimeSeparator : ':';
  this.YearFrom = intYearFrom != null ? intYearFrom : 0;
  this.YearTo = intYearTo != null ? intYearTo : 9999;
  this.WeekFromMonday = (blnWeekFromMonday == null) || blnWeekFromMonday;
  this.ResourceStrings = objResourceStrings != null ? objResourceStrings : objEnglishStrings;
  this.Date = new Date();
  this.Midnight = 'AM';
  arrCalendars[this.ID] = this;
  document.write('<a href="javascript:arrCalendars[' + this.ID + '].Show()">' +
    '<img src="' + strCalendarScriptPath + 'calendar.gif" class=clsCalendarImage ' +
    'align=absmiddle title="' + this.ResourceStrings.SelectDateHint + '"></a>');
}

CalendarClass.prototype.DateToScript = function (objDate) {
  return('parent.arrCalendars[' + this.ID + '].SetDate(' +
    objDate.getFullYear() + ', ' + objDate.getMonth() + ', ' + objDate.getDate() + ')');
}

CalendarClass.prototype.GetDate = function () {
  switch (this.DateFormat) {
    case fmtDDMMYYYY:
      this.ParseDate('^(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{4})$', 3, 2, 1);
      return(true);
    case fmtDDMMYYYY12:
      this.ParseDate('^(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{4}) ' +
        '(\\d{1,2})\\' + this.TimeSeparator + '(\\d{1,2}) (AM|PM)$', 3, 2, 1, 4, 5, 6);
      return(true);
    case fmtDDMMYYYY24:
      this.ParseDate('^(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{4}) ' +
        '(\\d{1,2})\\' + this.TimeSeparator + '(\\d{1,2})$', 3, 2, 1, 4, 5);
      return(true);
    case fmtMMDDYYYY:
      this.ParseDate('^(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{4})$', 3, 1, 2);
      return(true);
    case fmtMMDDYYYY12:
      this.ParseDate('^(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{4}) ' +
        '(\\d{1,2})\\' + this.TimeSeparator + '(\\d{1,2}) (AM|PM)$', 3, 1, 2, 4, 5, 6);
      return(true);
    case fmtMMDDYYYY24:
      this.ParseDate('^(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{4}) ' +
        '(\\d{1,2})\\' + this.TimeSeparator + '(\\d{1,2})$', 3, 1, 2, 4, 5);
      return(true);
    case fmtYYYYMMDD24:
      this.ParseDate('^(\\d{4})\\' + this.DateSeparator + '(\\d{1,2})\\' + this.DateSeparator + '(\\d{1,2}) ' +
        '(\\d{1,2})\\' + this.TimeSeparator + '(\\d{1,2})\\' + this.TimeSeparator + '(\\d{1,2})$', 1, 2, 3, 4, 5);
      return(true);
    default:
      alert(this.ResourceStrings.WrongDateFormatError);
      return(false);
  }
}

CalendarClass.prototype.GetDocument = function () {
  return(CalendarFrame.document);
}

CalendarClass.prototype.Hide = function () {
  HideCalendar();
}

CalendarClass.prototype.ParseDate = function (strRegExp, intYearIndex, intMonthIndex, intDayIndex,
   intHourIndex, intMinuteIndex, intMidnightIndex) {
  var objDate, strMidnight;
  var strDate = document.getElementById(this.DateObjectID).value;
  var objRegExp = new RegExp(strRegExp, 'i');
  var arrMatches = objRegExp.exec(strDate);
  if (arrMatches != null) {
    objDate = new Date(arrMatches[intYearIndex], arrMatches[intMonthIndex] - 1, arrMatches[intDayIndex],
      intHourIndex != null ? arrMatches[intHourIndex] : null,
      intMinuteIndex != null ? arrMatches[intMinuteIndex] : null);
    if (intMidnightIndex != null)
      strMidnight = arrMatches[intMidnightIndex].toUpperCase();
  }
  if (isNaN(objDate)) {
    if (strDate != '')
      alert(this.ResourceStrings.InvalidDateError.replace('%1', strDate));
    objDate = new Date();
    if(intMidnightIndex != null)
    {
    strMidnight = objDate.getHours() > 11 ? 'PM' : 'AM';
    if (objDate.getHours() > 12)
      objDate.setHours(objDate.getHours() - 12);
    }  
  }
  this.Date = new Date(objDate);
  this.Midnight = strMidnight;
}

CalendarClass.prototype.Refresh = function () {
  var strMonth = '', strTime = '', strClassName;
  var objToday = new Date();
  var intDateOffset = this.WeekFromMonday ? 1 : 0;
  for (var i = intDateOffset; i < 7; i++)
    strMonth += '<td class=clsCalendarDayName>' + this.ResourceStrings.DayNames[i] + '</td>';
  if (this.WeekFromMonday)
    strMonth += '<td class=clsCalendarDayName>' + this.ResourceStrings.DayNames[0] + '</td>';
  var objFirstDate = new Date(this.Date.getFullYear(), this.Date.getMonth(), 1);
  var objLastDate = new Date(this.Date.getFullYear(), this.Date.getMonth() + 1, 0);
  var intFirstDateOffset = (objFirstDate.getDay() > 0) || !this.WeekFromMonday ? objFirstDate.getDay() - intDateOffset : 6;
  var intLastDateOffset = (objLastDate.getDay() > 0) || !this.WeekFromMonday ? 7 - objLastDate.getDay() + intDateOffset : 1;
  objFirstDate.setDate(objFirstDate.getDate() - intFirstDateOffset);
  objLastDate.setDate(objLastDate.getDate() + intLastDateOffset);
  strMonth = '<tr>' + strMonth + '</tr><tr align=right>';
  while (!SameDate(objFirstDate, objLastDate)) {
    var strStyle = '';
    if (objFirstDate.getMonth() != this.Date.getMonth())
      strClassName = 'clsCalendarDiffMonthDay';
    else {
      strClassName = (objFirstDate.getDay() == 6) || (objFirstDate.getDay() == 0) ? 'clsCalendarRestDay' : 'clsCalendarWorkDay';
      if (SameDate(objFirstDate, objToday))
        strClassName += ' clsCalendarToday';
      if (objFirstDate.getDate() == this.Date.getDate())
        strClassName += ' clsCalendarSelDay';
    }
    strMonth += '<td class="' + strClassName + '" onclick="' + this.DateToScript(objFirstDate) + '" ' +
      'onmouseover="parent.AddElementClass(this, \'clsCalendarOverDay\')" ' +
      'onmouseout="parent.RemoveElementClass(this, \'clsCalendarOverDay\')">' + objFirstDate.getDate() + '</td>';
    if (objFirstDate.getDay() == (this.WeekFromMonday ? 0 : 6))
      strMonth += '</tr><tr align=right>';
    objFirstDate.setDate(objFirstDate.getDate() + 1);
  }
  strMonth = strMonth.substr(0, strMonth.length - '<tr align=right>'.length);
  var strButtonEvents = ' onmouseover="parent.AddElementClass(this, \'clsCalendarOverButton\')" ' +
    'onmouseout="parent.RemoveElementClass(this, \'clsCalendarOverButton\')"';
  if ((this.DateFormat != fmtMMDDYYYY) && (this.DateFormat != fmtDDMMYYYY)) {
    var strMidnight = (this.DateFormat == fmtMMDDYYYY12) || (this.DateFormat == fmtDDMMYYYY12) ?
      '<td id=objMidnightButton class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShowMidnightList()"' +
      strButtonEvents + '>' + this.Midnight + '</td>' : '<td class=clsCalendarTimeCaption>&nbsp;</td>';
    strTime = '<tr><td colspan=3 class=clsCalendarTimeCaption>' + this.ResourceStrings.TimeCaption + '</td>' +
      '<td id=objHourButton class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShowHourList()"' +
      strButtonEvents + ' title="' + this.ResourceStrings.SelectHoursHint + '">' + IntToStr(this.Date.getHours()) + '</td>' +
      '<td class=clsCalendarTimeCaption>' + this.TimeSeparator + '</td>' +
      '<td id=objMinuteButton class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShowMinuteList()"' +
      strButtonEvents + ' title="' + this.ResourceStrings.SelectMinutesHint + '">' +
      IntToStr(this.Date.getMinutes()) + '</td>' + strMidnight + '</tr>';
  }
  var strBody = '<table id=objTable class=clsCalendarTable cellspacing=0><tr>' +
    '<td class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShiftDate(-1, true)"' +
    strButtonEvents + ' title="' + this.ResourceStrings.PriorMonthHint + '">«</td>' +
    '<td colspan=3 id=objMonthButton class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShowMonthList()"' +
    strButtonEvents + ' title="' + this.ResourceStrings.SelectMonthHint + '">' + this.ResourceStrings.MonthNames[this.Date.getMonth()] + '</td>' +
    '<td colspan=2 id=objYearButton class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShowYearList()"' +
    strButtonEvents + ' title="' + this.ResourceStrings.SelectYearHint + '">' + this.Date.getFullYear() + '</td>' +
    '<td class=clsCalendarButton onclick="parent.arrCalendars[' + this.ID + '].ShiftDate(1, true)"' +
    strButtonEvents + ' title="' + this.ResourceStrings.NextMonthHint + '">»</td></tr>' +
    strMonth + '<tr><td colspan=7 class=clsCalendarTodayCaption onclick="' + this.DateToScript(objToday) + '">' +
    this.ResourceStrings.TodayCaption + ': ' + this.ResourceStrings.MonthNames[objToday.getMonth()].substr(0, 3) + ' ' + objToday.getDate() + ', ' +
    objToday.getFullYear() + '</td></tr>' + strTime + '</table><div id=objDropDownList class=clsCalendarDropDownList ' +
    'onmouseover="clearTimeout(parent.intTimeoutID)" onmouseout="parent.intTimeoutID = setTimeout(\'parent.arrCalendars[' +
    this.ID + '].SetDropDownListVisibility(false)\', 100)" style="display: none"></div>';
  if (!blnFrameInitialized) {
    this.GetDocument().write('<html><head><link href="' + strCalendarScriptPath + 'calendar.css" ' +
      'rel=stylesheet type="text/css"></head><body class=clsCalendarBody onselectstart="return(false)" ' +
      'onload="setTimeout(\'parent.ResizeCalendar()\', 200)">' + strBody + '</body></html>');
    this.GetDocument().write(''); // for Mac
    this.GetDocument().close();
    blnFrameInitialized = true;
  } else {
    this.GetDocument().body.innerHTML = strBody;
    ResizeCalendar();
  }
}

CalendarClass.prototype.SetDate = function (intYear, intMonth, intDay) {
  var strDate;
  if ((intYear < this.YearFrom) || (intYear > this.YearTo)) {
    alert(this.ResourceStrings.OutOfYearRangeError.replace('%1', this.YearFrom).replace('%2', this.YearTo));
    return;
  }
  intMonth++;
  switch (this.DateFormat) {
    case fmtDDMMYYYY:
      strDate = IntToStr(intDay) + this.DateSeparator + IntToStr(intMonth) + this.DateSeparator + intYear;
      break;
    case fmtDDMMYYYY12:
      strDate = IntToStr(intDay) + this.DateSeparator + IntToStr(intMonth) + this.DateSeparator + intYear + ' ' +
        this.GetDocument().getElementById('objHourButton').innerHTML + this.TimeSeparator +
        this.GetDocument().getElementById('objMinuteButton').innerHTML + ' ' + this.Midnight;
      break;
    case fmtDDMMYYYY24:
      strDate = IntToStr(intDay) + this.DateSeparator + IntToStr(intMonth) + this.DateSeparator + intYear + ' ' +
        this.GetDocument().getElementById('objHourButton').innerHTML + this.TimeSeparator +
        this.GetDocument().getElementById('objMinuteButton').innerHTML;
      break;
    case fmtMMDDYYYY:
      strDate = IntToStr(intMonth) + this.DateSeparator + IntToStr(intDay) + this.DateSeparator + intYear;
      break;
    case fmtMMDDYYYY12:
      strDate = IntToStr(intMonth) + this.DateSeparator + IntToStr(intDay) + this.DateSeparator + intYear + ' ' +
        this.GetDocument().getElementById('objHourButton').innerHTML + this.TimeSeparator +
        this.GetDocument().getElementById('objMinuteButton').innerHTML + ' ' + this.Midnight;
      break;
    case fmtMMDDYYYY24:
      strDate = IntToStr(intMonth) + this.DateSeparator + IntToStr(intDay) + this.DateSeparator + intYear + ' ' +
        this.GetDocument().getElementById('objHourButton').innerHTML + this.TimeSeparator +
        this.GetDocument().getElementById('objMinuteButton').innerHTML;
      break;
    case fmtYYYYMMDD24:
      strDate = intYear + this.DateSeparator + IntToStr(intMonth) + this.DateSeparator + IntToStr(intDay) + ' ' +
        this.GetDocument().getElementById('objHourButton').innerHTML + this.TimeSeparator +
        this.GetDocument().getElementById('objMinuteButton').innerHTML + this.TimeSeparator + '00';
      break;
  }
  document.getElementById(this.DateObjectID).value = strDate;
  this.Hide();
}

CalendarClass.prototype.SetDropDownListVisibility = function (blnVisible) {
  this.GetDocument().getElementById('objDropDownList').style.display = blnVisible ? '' : 'none';
}

CalendarClass.prototype.SetHours = function (intHours) {
  this.Date.setHours(intHours);
  this.GetDocument().getElementById('objHourButton').innerHTML = IntToStr(intHours);
  this.SetDropDownListVisibility(false);
}

CalendarClass.prototype.SetMidnight = function (strMidnight) {
  this.Midnight = strMidnight;
  this.GetDocument().getElementById('objMidnightButton').innerHTML = this.Midnight;
  this.SetDropDownListVisibility(false);
}

CalendarClass.prototype.SetMinutes = function (intMinutes) {
  this.Date.setMinutes(intMinutes);
  this.GetDocument().getElementById('objMinuteButton').innerHTML = IntToStr(intMinutes);
  this.SetDropDownListVisibility(false);
}

CalendarClass.prototype.ShiftDate = function (intOffset, blnMonthOffset, blnAbsoluteOffset) {
  var intDay = this.Date.getDate();
  if (blnMonthOffset)
    this.Date.setMonth(intOffset + (blnAbsoluteOffset ? 0 : this.Date.getMonth()));
  else
    this.Date.setFullYear(intOffset + (blnAbsoluteOffset ? 0 : this.Date.getFullYear()));
  if (this.Date.getDate() != intDay)
    this.Date.setDate(0);
  this.Refresh();
}

CalendarClass.prototype.Show = function () {
  this.Hide();
  if (!this.GetDate())
    return;
  this.Refresh();
  var objDateObject = document.getElementById(this.DateObjectID);
  var objPos = GetElementPos(objDateObject);
  var objCalendarFrame = document.getElementById('objCalendarFrame');
  objCalendarFrame.style.left = objPos.x + 'px';
  objCalendarFrame.style.top = (objPos.y + objDateObject.offsetHeight) + 'px';
  if (document.attachEvent)
    document.attachEvent('onmousedown', HideCalendar);
  else if (document.addEventListener)
    document.addEventListener('mousedown', HideCalendar, true);
  else
    document.onmousedown = HideCalendar;
}

CalendarClass.prototype.ShowHourList = function () {
  this.SetDropDownListVisibility(false);
  var objDropDownList = this.GetDocument().getElementById('objDropDownList');
  var strHourList = '';
  var blnMidnightFormat = (this.DateFormat == fmtMMDDYYYY12) || (this.DateFormat == fmtDDMMYYYY12);
  for (var i = (blnMidnightFormat ? 1 : 0); i < (blnMidnightFormat ? 13 : 24); i++)
    strHourList += '<div class=clsCalendarListItem onclick="parent.arrCalendars[' + this.ID +
      '].SetHours(' + i + ')"' + strListItemEvents + '>' + IntToStr(i) + '</div>';
  objDropDownList.innerHTML = strHourList;
  var objHourButton = this.GetDocument().getElementById('objHourButton');
  objDropDownList.style.left = GetElementPos(objHourButton).x + 'px';
  objDropDownList.style.top = '1px';
  objDropDownList.style.width = '42px';
  objDropDownList.style.height =
    (this.GetDocument().getElementById('objTable').offsetHeight - objHourButton.offsetHeight - 2) + 'px';
  this.SetDropDownListVisibility(true);
}

CalendarClass.prototype.ShowMidnightList = function () {
  this.SetDropDownListVisibility(false);
  var objDropDownList = this.GetDocument().getElementById('objDropDownList');
  objDropDownList.innerHTML = '<div class=clsCalendarListItem onclick="parent.arrCalendars[' + this.ID +
    '].SetMidnight(\'AM\')"' + strListItemEvents + '>AM</div><div class=clsCalendarListItem ' +
    'onclick="parent.arrCalendars[' + this.ID + '].SetMidnight(\'PM\')"' + strListItemEvents + '>PM</div>';
  var objMidnightButton = this.GetDocument().getElementById('objMidnightButton');
  var intHeight = objMidnightButton.offsetHeight * 2 - 5;
  objDropDownList.style.left = GetElementPos(objMidnightButton).x + 'px';
  objDropDownList.style.top =
    (this.GetDocument().getElementById('objTable').offsetHeight - objMidnightButton.offsetHeight - intHeight - 1) + 'px';
  objDropDownList.style.width = '29px';
  objDropDownList.style.height = intHeight + 'px';
  this.SetDropDownListVisibility(true);
}

CalendarClass.prototype.ShowMinuteList = function () {
  this.SetDropDownListVisibility(false);
  var objDropDownList = this.GetDocument().getElementById('objDropDownList');
  var strMinuteList = '';
  for (var i = 0; i < 60; i++)
    strMinuteList += '<div class=clsCalendarListItem onclick="parent.arrCalendars[' + this.ID +
      '].SetMinutes(' + i + ')"' + strListItemEvents + '>' + IntToStr(i) + '</div>';
  objDropDownList.innerHTML = strMinuteList;
  var objMinuteButton = this.GetDocument().getElementById('objMinuteButton');
  objDropDownList.style.left = GetElementPos(objMinuteButton).x + 'px';
  objDropDownList.style.top = '1px';
  objDropDownList.style.width = '42px';
  objDropDownList.style.height =
    (this.GetDocument().getElementById('objTable').offsetHeight - objMinuteButton.offsetHeight - 2) + 'px';
  this.SetDropDownListVisibility(true);
}

CalendarClass.prototype.ShowMonthList = function () {
  this.SetDropDownListVisibility(false);
  var objDropDownList = this.GetDocument().getElementById('objDropDownList');
  var strMonthList = '';
  for (var i = 0; i < this.ResourceStrings.MonthNames.length; i++)
    strMonthList += '<div class=clsCalendarListItem onclick="parent.arrCalendars[' + this.ID +
      '].ShiftDate(' + i + ', true, true)"' + strListItemEvents + '>' + this.ResourceStrings.MonthNames[i] + '</div>';
  objDropDownList.innerHTML = strMonthList;
  var objMonthButton = this.GetDocument().getElementById('objMonthButton');
  var objPos = GetElementPos(objMonthButton);
  objDropDownList.style.left = objPos.x + 'px';
  var intTop = objPos.y + objMonthButton.offsetHeight;
  objDropDownList.style.top = intTop + 'px';
  objDropDownList.style.width = '87px';
  objDropDownList.style.height = (this.GetDocument().getElementById('objTable').offsetHeight - intTop - 1) + 'px';
  this.SetDropDownListVisibility(true);
}

CalendarClass.prototype.ShowYearList = function () {
  this.SetDropDownListVisibility(false);
  var objDropDownList = this.GetDocument().getElementById('objDropDownList');
  var strYearList = '';
  for (var i = 0; i < arrYearOffsets.length; i++) {
    var intYear = this.Date.getFullYear() + arrYearOffsets[i];
    strYearList += '<div class="clsCalendarListItem' +
      (intYear == this.Date.getFullYear() ? ' clsCalendarSelListItem' : '') + '" ' +
      'onclick="parent.arrCalendars[' + this.ID + '].ShiftDate(' + intYear + ', false, true)"' +
      strListItemEvents + '>' + intYear + '</div>';
  }
  objDropDownList.innerHTML = strYearList;
  var objYearButton = this.GetDocument().getElementById('objYearButton');
  var objPos = GetElementPos(objYearButton);
  objDropDownList.style.left = objPos.x + 'px';
  var intTop = objPos.y + objYearButton.offsetHeight;
  objDropDownList.style.top = intTop + 'px';
  objDropDownList.style.width = '56px';
  objDropDownList.style.height = (this.GetDocument().getElementById('objTable').offsetHeight - intTop - 1) + 'px';
  this.SetDropDownListVisibility(true);
}
