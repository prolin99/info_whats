Option Explicit
On Error Resume Next

Const ForReading = 1
Const TextCompare = 1
Const OpenAsUnicode = true


Dim File1, File2, OutputFile
Dim TextContent1,TextContent2

File1 = WScript.Arguments(0)
File2 = WScript.Arguments(1)


Dim ObjFSO : Set ObjFSO = CreateObject("Scripting.FileSystemObject")
'msgbox File1

Dim Text1_lines , Text2_lines

If ObjFSO.FileExists(File1) Then

  Dim objFile1 : Set objFile1 = objFSO.OpenTextFile(File1, ForReading,0 , OpenAsUnicode)
  TextContent1 =  objFile1.ReadAll()
  objFile1.close
  'msgbox  TextContent1
  Text1_lines = Split(TextContent1, vbCrLf)

Else

  WScript.Quit
End If

'msgbox File2

If ObjFSO.FileExists(File2) Then
  Dim objFile2 : Set objFile2 = objFSO.OpenTextFile(File2, ForReading,0 , OpenAsUnicode)
  TextContent2 =  objFile2.ReadAll()
  objFile2.close
  'msgbox  TextContent2
  Text2_lines = Split(TextContent2, vbCrLf)
Else
  WScript.Quit
End If


'If (StrComp(trim(TextContent1),trim(TextContent2)) <>0) Then
''   Msgbox "hardware check maybe  error ."
'end if

Dim li , has_err

For li = 1 to 7  step 2
   if (StrComp(Text1_lines(li) , Text2_lines(li) ) <>0 ) then
      'MsgBox Text1_lines(li) & "--" & Text2_lines(li)
      has_err = 1
  end if
Next

if (has_err = 1) then
  MsgBox "hardware check maybe  error ."
end if
