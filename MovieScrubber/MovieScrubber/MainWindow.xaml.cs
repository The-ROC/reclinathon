using System;
using System.Collections.Generic;
using System.Collections;
using System.Text;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;
using MovieDatabase;
using System.Collections.ObjectModel;
using System.Net;
using System.IO;
//using HtmlAgilityPack;

namespace MovieScrubber
{
	public class DataItems
       	{
              public bool Selected { get; set; }
              public string Name { get; set; }
              
              public DataItems()
              {
                     this.Selected = false;
                     this.Name = "Item";
              }
              
              public DataItems(string item)
              {
                     this.Selected = true;
                     this.Name = item;
              }
			
			  public DataItems(string item, bool check)
              {
                     this.Selected = check;
                     this.Name = item;
              }
			
              
       	}
	
		public class DataItemsCollection : ObservableCollection<DataItems>
    	{	
              
    	}
		
	/// <summary>
	/// Interaction logic for MainWindow.xaml
	/// </summary>
	public partial class MainWindow : Window
	{
		private DataItemsCollection GenreList;
		private DataItemsCollection CastList;
		private ArrayList PosterList;
		private int PIndex;
		private ArrayList FreshScoreList;
		private ArrayList FreshNameList;
		private int FIndex;
		private ArrayList MetaScoreList;
		private ArrayList MetaNameList;
		private int MIndex;
		
		public MainWindow()
		{
			GenreList = new DataItemsCollection();
			CastList = new DataItemsCollection();
			PosterList = new ArrayList();
			//PosterList.Add("http://www.reclinathon.com/movie_reel.jpg");
			PIndex = 0;
			//Poster.Source = new System.Windows.Media.Imaging.BitmapImage(new System.Uri(PosterList[PIndex].ToString()));
			FreshScoreList = new ArrayList();
			FreshNameList = new ArrayList();
			FIndex = 0;
			MetaScoreList = new ArrayList();
			MetaNameList = new ArrayList();
			MIndex = 0;
			this.InitializeComponent();
			WebGrid.Visibility = System.Windows.Visibility.Hidden;
			BrowserGrid.Visibility = System.Windows.Visibility.Hidden;
			// Insert code required on object creation below this point.
			GenreGrid.ItemsSource = GenreList;
			CastGrid.ItemsSource = CastList;
			//GenreList.Add(new DataItems("Hat"));
		}

		private void ToggleWeb(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (WebGrid.Visibility == System.Windows.Visibility.Visible)
			{
				WebGrid.Visibility = System.Windows.Visibility.Hidden;
				BrowserGrid.Visibility = System.Windows.Visibility.Hidden;
				this.Window.WindowState = System.Windows.WindowState.Normal;
			}
			else
			{
				WebGrid.Visibility = System.Windows.Visibility.Visible;
				BrowserGrid.Visibility = System.Windows.Visibility.Visible;
				this.Window.WindowState = System.Windows.WindowState.Maximized;
			}
		}

		private void SearchClick(object sender, System.Windows.RoutedEventArgs e)
		{
			string query = SearchBox.Text;
			string IMDB = CodeBox.Text;
			bool success = true;
			
			MovieInfo m = new MovieInfo();
			 
			if (sender == UpdateIMDB)
			{
				success = m.Search(query,IMDB);	
			}
			else
			{
				success = m.Search(query);	
			}
			
			
            if (success)
			{
				TitleBox.Text = m.Title;
				YearBox.Text = m.Year;
				DirectorBox.Text = m.Director;
				RunBox.Text = m.Runtime;
				CodeBox.Text = m.Id;
				
				//FreshBox.Text = m.Freshness;
				//FreshTitle.Text = m.FreshTitle;
				FreshScoreList.Clear();
				FreshNameList.Clear();
				FIndex = 0;
				string fresh = m.Freshness;
				string fname = m.FreshTitle;
				string[] fslist = fresh.Split('|');
				string[] fnlist = fname.Split('|');
				foreach (string fs in fslist)
				{
					if(fs != "")
					{
						FreshScoreList.Add(fs);
					}
				}
				foreach (string fn in fnlist)
				{
					if(fn != "")
					{
						FreshNameList.Add(fn);
					}
				}
				
				if (FreshScoreList.Count > 0)
				{
					FreshBox.Text = FreshScoreList[FIndex].ToString();
					FreshTitle.Text = FreshNameList[FIndex].ToString();
				}
				else
				{
					FreshBox.Text = "";
					FreshTitle.Text = "";
				}
				
				//MetaBox.Text = m.MetaScore;
				//MetaTitle.Text = m.MetaTitle;
				MetaScoreList.Clear();
				MetaNameList.Clear();
				MIndex = 0;
				string meta = m.MetaScore;
				string mname = m.MetaTitle;
				string[] mslist = meta.Split('|');
				string[] mnlist = mname.Split('|');
				foreach (string ms in mslist)
				{
					if(ms != "")
					{
						MetaScoreList.Add(ms);
					}
				}
				foreach (string mn in mnlist)
				{
					if(mn != "")
					{
						MetaNameList.Add(mn);
					}
				}
				if (MetaScoreList.Count > 0)
				{
					MetaBox.Text = MetaScoreList[MIndex].ToString();
					MetaTitle.Text = MetaNameList[MIndex].ToString();
				}
				else
				{
					MetaBox.Text = "";
					MetaTitle.Text = "";
				}
				
				ImageLink.Text = m.PosterLink;
				IMDBLink.Text = m.Link;
				TrailerLink.Text = m.TrailerLink;
				
				//System.Uri myPoster = new System.Uri("http://upload.wikimedia.org/wikipedia/en/9/91/Inception_poster.jpg");
				//Poster.Source = m.PosterImage; //new BitmapImage(myPoster);
				PosterList.Clear();
				PIndex = 0;
				string posters = m.PosterLink;
				string[] plist = posters.Split('|');
				foreach (string p in plist)
				{
					if(p != "" && !PosterList.Contains(p))
					{
						PosterList.Add(p);
					}
				}
				
				if (PosterList.Count > 0)
				{
					ImageLink.Text = PosterList[PIndex].ToString();
					UpdateImage(null,null);
				}
				else
				{
					ImageLink.Text = "";
					Poster.Source = new System.Windows.Media.Imaging.BitmapImage(new System.Uri("http://www.reclinathon.com/movie_reel.jpg"));
				}
				
				GenreList.Clear();
				string genre = m.Genre;
				string[] glist = genre.Split('|');
				foreach (string g in glist)
				{
					if(g != "")
					{
						GenreList.Add(new DataItems(g));
					}
				}
				
				CastList.Clear();
				string actor = m.Cast;
				string[] alist = actor.Split('|');
				foreach (string a in alist)
				{
					if(a != "")
					{
						if(CastList.Count < 5)
						{
							CastList.Add(new DataItems(a, true));
						}
						else
						{
							CastList.Add(new DataItems(a,false));
						}
					}
				}
				
				XmlBox.Text = "Server Response";
				
				/*
				CastList.Items.Clear();
				string cast = m.Cast;
				string[] clist = cast.Split('|');
				for (int i = 0; i<10; i++)
				{
					CastList.Items.Add(clist[i]);
				}
				
				ATest.Text = m.Cast;*/
			}
			else
			{
			TitleBox.Text = "BROKEN!!";
			}
		}

		private void UpdateBrowser(object sender, System.Windows.Input.MouseButtonEventArgs e)
		{
			// TODO: Add event handler implementation here.
			string search = ((TextBox)sender).Text;
			if (sender == BrowserBar)
			{
				search = "http://www.bing.com/search?q="+search;
			}
			
			MiniBrowser.Source = new Uri(search);  //(((TextBox)sender).Text);
			
			if (sender != BrowserBar)
			{
				ToggleWeb(null,null);
			}
		}

		private void UpdateImage(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			Poster.Source = new BitmapImage(new Uri(ImageLink.Text));
		}

		private void PBack(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (PosterList.Count == 0)
			{
				return;
			}
			
			if (PIndex == 0)
			{
				PIndex = PosterList.Count - 1;
			}
			else
			{
				PIndex --;
			}
			ImageLink.Text = PosterList[PIndex].ToString();
			UpdateImage(null,null);
		}

		private void PNext(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (PosterList.Count == 0)
			{
				return;
			}
			
			if (PIndex == PosterList.Count - 1)
			{
				PIndex = 0;
			}
			else
			{
				PIndex ++;
			}
			ImageLink.Text = PosterList[PIndex].ToString();
			UpdateImage(null,null);
		}
		
		private void FBack(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (FreshScoreList.Count == 0)
			{
				return;
			}
			
			if (FIndex == 0)
			{
				FIndex = FreshScoreList.Count - 1;
			}
			else
			{
				FIndex --;
			}
			FreshBox.Text = FreshScoreList[FIndex].ToString();
			FreshTitle.Text = FreshNameList[FIndex].ToString();
		}

		private void FNext(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (FreshScoreList.Count == 0)
			{
				return;
			}
			
			if (FIndex == FreshScoreList.Count - 1)
			{
				FIndex = 0;
			}
			else
			{
				FIndex ++;
			}
			FreshBox.Text = FreshScoreList[FIndex].ToString();
			FreshTitle.Text = FreshNameList[FIndex].ToString();
		}

		private void MBack(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (MetaScoreList.Count == 0)
			{
				return;
			}
			
			if (MIndex == 0)
			{
				MIndex = MetaScoreList.Count - 1;
			}
			else
			{
				MIndex --;
			}
			MetaBox.Text = MetaScoreList[MIndex].ToString();
			MetaTitle.Text = MetaNameList[MIndex].ToString();
		}

		private void MNext(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			if (MetaScoreList.Count == 0)
			{
				return;
			}
			
			if (MIndex == MetaScoreList.Count - 1)
			{
				MIndex = 0;
			}
			else
			{
				MIndex ++;
			}
			MetaBox.Text = MetaScoreList[MIndex].ToString();
			MetaTitle.Text = MetaNameList[MIndex].ToString();
		}
		
		private string GetXmlString()
		{
			//char[] GenreSeperator = {'|', ' '};
			//string[] Genres = GenreBox.Text.Split(GenreSeperator);
			//char[] ScoreSeperator = {'%', ',', ' '};
			//string[] Scores = FreshnessBox.Text.Split(ScoreSeperator);
			string MovieXml = "<RWSRequest><Header>";
			MovieXml += "<TimeStamp>" + System.DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss") + "</TimeStamp>";
			MovieXml += "<Season>Winter2010</Season>";
			MovieXml += "<Command>AddToMovieDatabase</Command>";
			MovieXml += "</Header><Data><Movies><Movie>";
			MovieXml += "<Title>" + TitleBox.Text + "</Title>";
			MovieXml += "<Year>" + YearBox.Text + "</Year>";
			MovieXml += "<Director>" + DirectorBox.Text + "</Director>";
			MovieXml += "<IMDBLink>" + IMDBLink.Text + "</IMDBLink>";
			MovieXml += "<PosterLink>" + ImageLink.Text + "</PosterLink>";
			MovieXml += "<Freshness>" + FreshBox.Text + "</Freshness>";
			MovieXml += "<MetaScore>" + MetaBox.Text + "</MetaScore>";
			MovieXml += "<RunTime>" + RunBox.Text + "</RunTime>";
			MovieXml += "<TrailerLink>" + TrailerLink.Text.Replace("&","*").Replace("?","%3f").Replace("=","%3d") + "</TrailerLink>";
			MovieXml += "<Genres>";
			foreach (DataItems genre in GenreList) 
			{
				if (genre.Selected)
				{
					MovieXml += "<Genre>" + genre.Name + "</Genre>";
				}
			}
			MovieXml += "</Genres>";
			MovieXml += "<Actors>";
			foreach (DataItems actor in CastList) 
			{
				if (actor.Selected)
				{
					MovieXml += "<Actor>" + actor.Name + "</Actor>";
				}
			}
			MovieXml += "</Actors>";
			MovieXml += "</Movie></Movies><MovieLists><MovieList>Ballot</MovieList></MovieLists></Data></RWSRequest>";
			
			return MovieXml;
		}

		private void ReclinathonWebRequest()
		{
			string WebText = "";
			HttpWebRequest ReclinathonRequest = (HttpWebRequest) WebRequest.Create("http://www.reclinathon.com/rtt/rws.php");
			
			// Set the 'Method' property of the 'Webrequest' to 'POST'.
            ReclinathonRequest.Method = "POST";
			
            // Create a new string object to POST data to the Url.
            string inputData = GetXmlString();

            string postData = "RWSRequest=" + inputData;
            ASCIIEncoding encoding = new ASCIIEncoding ();
            byte[] byte1 = encoding.GetBytes (postData);

            // Set the content type of the data being posted.
            ReclinathonRequest.ContentType = "application/x-www-form-urlencoded";

            // Set the content length of the string being posted.
            ReclinathonRequest.ContentLength = byte1.Length;

            Stream newStream = ReclinathonRequest.GetRequestStream ();

            newStream.Write (byte1, 0, byte1.Length);

            // Close the Stream object.
            newStream.Close ();
			
			HttpWebResponse response = (HttpWebResponse)ReclinathonRequest.GetResponse ();

            // Get the stream associated with the response.
            Stream receiveStream = response.GetResponseStream ();

            // Pipes the stream to a higher level stream reader with the required encoding format. 
            StreamReader readStream = new StreamReader (receiveStream, Encoding.UTF8);

            WebText += (readStream.ReadToEnd ());
            response.Close ();
            readStream.Close ();
			
			XmlBox.Text = WebText;
		}		
		
		private void makexml(object sender, System.Windows.RoutedEventArgs e)
		{
			// TODO: Add event handler implementation here.
			//XmlBox.Text = GetXmlString();
			ReclinathonWebRequest();
		}

		private void DisplayHtml(object sender, System.Windows.Input.MouseButtonEventArgs e)
		{
			// TODO: Add event handler implementation here.
			MiniBrowser.NavigateToString(XmlBox.Text);
			ToggleWeb(null,null);
		}
	}
}