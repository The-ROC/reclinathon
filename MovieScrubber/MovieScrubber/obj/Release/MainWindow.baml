   M S B A M L   `   `   `  ����   MovieScrubber    MovieScrubber.MainWindow   \ XPresentationFramework, Version=4.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35W SPresentationCore, Version=4.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35R NWindowsBase, Version=4.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35D 9http://schemas.microsoft.com/winfx/2006/xaml/presentation    R NSystem.Xaml, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089:x,http://schemas.microsoft.com/winfx/2006/xaml     @d2http://schemas.microsoft.com/expression/blend/2008     Jmc;http://schemas.openxmlformats.org/markup-compatibility/2006     -     �Name$  Window�� � Title$ 
MainWindow�� � UseLayoutRounding . $	��800��$	��600��.��� -    �Name$ 
LayoutRoot��.+�� -   $ DataGrid��$��	0,51,0,-1q�$��Right=�$	��784��.+��� -    ��Name$ TitleBox��$
��Left=�$��29��$��	13,16,0,0q� �� TextWrapping$
 Wrap=�$��Title��$	��Top=�$	��270���� -   $ YearBox��$
��Left=�$��29��$��	13,52,0,0q�$
 Wrap=�$
��Year��$	��Top=�$	��270���� -   $ DirectorBox��$
��Left=�$��29��$��	13,89,0,0q�$
 Wrap=�$��Director��$	��Top=�$	��270���� -   $ RunBox��$
��Left=�$��29��$��
13,125,0,0q�$
 Wrap=�$��Runtime��$	��Top=�$	��270���� -   $ FreshBox��$��Right=�$��29��$��
0,14,355,0q�$
 Wrap=�$	��---��$	��Top=�$��58���� -	   $ MetaBox��$��Right=�$��29��$��
0,89,355,0q�$
 Wrap=�$	��---��$	��Top=�$��58���� -
    ��Name$ 
FreshBlock��$��29��$��
307,16,0,0q�$
��Wrap=�$��
Freshness:��$	��Top=�$
��Left=�$��75���� -   $ 	MetaBlock��$��29��$��
307,95,0,0q�$
��Wrap=�$��
MetaScore:��$	��Top=�$
��Left=�$��75���� -   $ 
FreshTitle��$��24��$��307,57,209,0q�$
��Wrap=�$��RT Movie Title��$	��Top=����	#FFF3F3F3�� -   $ 	MetaTitle��$��24��$��307,130,209,0q�$
��Wrap=�$��MC Movie Title��$	��Top=����	#FFF3F3F3�� -    ��Name$ Poster��$��Right=�$��0,15,11,234q�$	��190��$��movie_reel.jpg��$
��Fill=��� -   $ 	ImageLink��$��Right=�$��48��$��
0,0,15,166q�$
 Wrap=�$��
Image Link��$��Bottom=�$	��318���� -   $ IMDBLink��$��Right=�$��32��$��
0,0,15,118q�$
 Wrap=�$��	IMDB Link��$��Bottom=�$	��318���� -   $ TrailerLink��$��Right=�$��48��$��	0,0,15,54q�$
 Wrap=�$��Trailer Link��$��Bottom=�$	��318���� -   $��Update��$��Right=�$��0,234,208,234q�$��46��&   System.Windows.Controls.DataGrid  -     Name$ 	GenreGrid��$
��Left=�$��13,191,0,118q�$	��180��	   AutoGenerateColumns	 .  ���� 
   Columns
 4  .System.Windows.Controls.DataGridCheckBoxColumn  ,  &System.Windows.Controls.DataGridColumn   Header$  ��   Width5  /System.Windows.Controls.DataGridLengthConverter$ 25 1  +System.Windows.Controls.DataGridBoundColumn   Binding �� *
Selected+0  *System.Windows.Controls.DataGridTextColumn  $ Primary Genres�� �� *Name+  -   $ CastGrid��$��208,191,371,118q�	 .  ���� 
   $  ��$ 25  �� *
Selected+  $ 	Main Cast�� �� *Name+�� -    ��Name$ 
PosterNext��$��->��$��Right=�$��28��$��0,202,209,0q�$	��Top=�$��29���� -   $ 
PosterBack��$��<-��$��Right=�$��28��$��0,202,242,0q�$	��Top=�$��29���� -   $ 	FreshNext��$��->��$��Right=�$��28��$��
0,13,279,0q�$	��Top=�$��29���� -   $ 	FreshBack��$��<-��$��Right=�$��28��$��
0,13,312,0q�$	��Top=�$��29���� -   $ MetaBack��$��<-��$��Right=�$��28��$��
0,90,312,0q�$	��Top=�$��29���� -   $ MetaNext��$��->��$��Right=�$��28��$��
0,90,279,0q�$	��Top=�$��29���� -   $ XmlBox��$��26��$��	0,0,28,17q�$
 Wrap=�$��Server Response��$��Bottom=�$��Right=�$	��305�����	#FFF3F3F3�� -   $ CodeBox��$
��Left=�$��29��$��
13,158,0,0q�$
 Wrap=�$��	IMDB Code��$	��Top=�$	��141�� �� 	UndoLimit$ 10���� -   $ 
UpdateIMDB��$��Update��$
��Left=�$��26��$��163,158,0,0q�$	��Top=�$��59���� -   $ CurrentPosterIndexBlock��$
��Left=�$��23��$��515,174,0,0q�$
��Wrap=�$��*��$	��Top=�$��21�����	#FFF3F3F3�� -   $ PosterCountBlock��$
��Left=�$��23��$��555,174,0,0q�$
��Wrap=�$��*��$	��Top=�$��21�����	#FFF3F3F3�� -    $ PosterIndexSeparatorBlock��$
��Left=�$��23��$��534,174,0,0q�$
��Wrap=�$��of��$	��Top=�$��21�����	#FFF3F3F3�� -!   $ Synopsis��$
��Left=�$��85��$��
13,417,0,0q�$
 Wrap=�$��Synopsis��$	��Top=�$	��400��� -"   $ WebGrid��$��0,50,0,0q�.+�(  "System.Windows.Controls.WebBrowser  -#     Name$ MiniBrowser��$��	0,0,0,-50q�   Source$ http://www.google.com?�� -$   $ BrowserGrid��$��	0,0,163,0q�$��50��$	��Top=����	#FFEBEBEB K� ZIndex$ 1��.+��� -%   $ 
BrowserBar��$��
17,12,8,13q�$
 Wrap=�� -&   $ ControlGrid��$��50��$	��Top=����	#FFEBEBEB.+��� -'   $ 	SearchBox��$
��Left=�$��
18,12,0,12q�$
 Wrap=�$��Movie Title��$	��247���� -(   $ SearchButton��$��Search��$��	279,6,0,6q�$
��Left=�$	��109���� -)   $ 	AddButton��$��Add to Database��$��Right=�$��	0,6,284,6q�$��98��
���   ����	#FF707070����  �� EndPoint$	 0,1&� �� 
StartPoint$	 0,0&�.���  � Color$ 	#FFF3F3F3�� � Offset$ 0Q�� $ 	#FFEBEBEB��$	 0.5Q�� $ 	#FFDDDDDD��$	 0.5Q�� $ 	#FFCDCDCD��$ 1Q��� -*   $ WebClose��$	��Web��$��Right=�$��
0,12,50,12q�$��91���� -+   $ SearchTimeTextBlock��$
��Left=�$��
513,10,0,0q�$
��Wrap=�$	��Top=�$��34��$	��133��$��24(����	#FF00B619� -,   $ MessageBarGrid��$
��Left=�$	��287��$	��Top=�$	��792��$��	0,133,0,0q����	#FF09577C$ 1��$y�Hidden=�.+��� --   $ MessageBarTextBlock��$
��Left=�$
��Wrap=�$��Searching...��$	��Top=�$	��150��$	��772��$��72(�$��	10,65,0,0q�
������� �� TextAlignment$ Center=��� -.   $  CurrentMetaScoreIndexBlock��$
��Left=�$��23��$��517,145,0,0q�$
��Wrap=�$��*��$	��Top=�$��21�����	#FFF3F3F3�� -/   $ MetaScoreCountBlock��$
��Left=�$��23��$��557,145,0,0q�$
��Wrap=�$��*��$	��Top=�$��21�����	#FFF3F3F3�� -0   $ MetaScoreIndexSeparator��$
��Left=�$��23��$��536,145,0,0q�$
��Wrap=�$��of��$	��Top=�$��21�����	#FFF3F3F3�� -1   $  CurrentFreshnessIndexBlock��$
��Left=�$��23��$��
517,68,0,0q�$
��Wrap=�$��*��$	��Top=�$��21�����	#FFF3F3F3�� -2   $ FreshnessCountBlock��$
��Left=�$��23��$��
557,68,0,0q�$
��Wrap=�$��*��$	��Top=�$��21�����	#FFF3F3F3�� -3   $ FreshnessSeparatorBlock��$
��Left=�$��23��$��
536,68,0,0q�$
��Wrap=�$��of��$	��Top=�$��21�����	#FFF3F3F3