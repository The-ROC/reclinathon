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
using System.Windows.Threading;
using System.Threading.Tasks;

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
        private Object PosterLock;
        private SortedList<int, string> PosterList;
        private int PosterIndex;
        private Object FreshnessLock;
		private ArrayList FreshScoreList;
		private ArrayList FreshNameList;
		private int FreshnessIndex;
        private object MetaScoreLock;
		private ArrayList MetaScoreList;
		private ArrayList MetaNameList;
		private int MetaScoreIndex;
		
		public MainWindow()
		{
            this.InitializeComponent();

            WebGrid.Visibility = System.Windows.Visibility.Hidden;
            BrowserGrid.Visibility = System.Windows.Visibility.Hidden;

            GenreList = new DataItemsCollection();
            GenreGrid.ItemsSource = GenreList;

            CastList = new DataItemsCollection();
            CastGrid.ItemsSource = CastList;

            PosterLock = new Object();
            PosterList = new SortedList<int, string>();
            PosterIndex = 0;

            FreshnessLock = new Object();
			FreshScoreList = new ArrayList();
			FreshNameList = new ArrayList();
			FreshnessIndex = 0;

            MetaScoreLock = new object();
			MetaScoreList = new ArrayList();
			MetaNameList = new ArrayList();
			MetaScoreIndex = 0;			
        }

		private void ToggleWeb(object sender, System.Windows.RoutedEventArgs e)
		{
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

        private void OnMovieInfoChanged(object sender, MovieInfoChangedEventArgs e)
        {
            Task updateUi = Task.Run(() => {
                this.Dispatcher.Invoke(() => {
                    MovieInfo m = sender as MovieInfo;

                    if (e.InfoType == MovieInfoChangedType.ImdbInfoAvailable)
                    {
                        MovieDetailsEventArgs args = e as MovieDetailsEventArgs;
                        IMDBLink.Text = args.imdbLink;
                        TitleBox.Text = args.movieTitle;
                        YearBox.Text = args.releaseYear;
                        DirectorBox.Text = args.directorName;
                        RunBox.Text = args.movieRuntime;
                        CodeBox.Text = args.imdbId;
                        Synopsis.Text = args.synopsisText;

                        GenreList.Clear();
                        foreach (string genre in args.genreList)
                        {
                            GenreList.Add(new DataItems(genre));
                        }

                        CastList.Clear();
                        foreach (string actor in args.castList)
                        {
                            CastList.Add(new DataItems(actor, (CastList.Count < 5)));
                        }
                    }
                    else if (e.InfoType == MovieInfoChangedType.TrailerInfoAvailable)
                    {
                        SingleStringMovieInfoEventArgs args = e as SingleStringMovieInfoEventArgs;
                        TrailerLink.Text = args.infoString;
                    }
                    else if (e.InfoType == MovieInfoChangedType.RottenTomatoesInfoAvailable)
                    {
                        MovieScoreEventArgs movieScoreArgs = e as MovieScoreEventArgs;

                        lock(FreshnessLock)
                        {
                            FreshScoreList.Clear();
                            FreshNameList.Clear();
                            FreshnessIndex = 0;

                            foreach (KeyValuePair<string, string> movieScore in movieScoreArgs.movieScoreList)
                            {
                                FreshScoreList.Add(movieScore.Value);
                                FreshNameList.Add(movieScore.Key);
                            }

                            if (FreshNameList.Count > 0)
                            {
                                FreshBox.Text = FreshScoreList[0].ToString();
                                FreshTitle.Text = FreshNameList[0].ToString();

                                CurrentFreshnessIndexBlock.Text = (FreshnessIndex + 1).ToString();
                                FreshnessCountBlock.Text = FreshNameList.Count.ToString();
                            }
                            else
                            {
                                FreshBox.Text = "";
                                FreshTitle.Text = "";
                            }
                        }
                    }
                    else if (e.InfoType == MovieInfoChangedType.MetaCriticInfoAvailable)
                    {
                        MovieScoreEventArgs movieScoreArgs = e as MovieScoreEventArgs;

                        lock(MetaScoreLock)
                        {
                            MetaScoreList.Clear();
                            MetaNameList.Clear();
                            MetaScoreIndex = 0;

                            foreach (KeyValuePair<string, string> movieScore in movieScoreArgs.movieScoreList)
                            {
                                MetaScoreList.Add(movieScore.Value);
                                MetaNameList.Add(movieScore.Key);
                            }

                            if (MetaScoreList.Count > 0)
                            {
                                MetaBox.Text = MetaScoreList[0].ToString();
                                MetaTitle.Text = MetaNameList[0].ToString();

                                CurrentMetaScoreIndexBlock.Text = (MetaScoreIndex + 1).ToString();
                                MetaScoreCountBlock.Text = MetaScoreList.Count.ToString();
                            }
                            else
                            {
                                MetaBox.Text = "";
                                MetaTitle.Text = "";
                            }
                        }
                    }
                    else if (e.InfoType == MovieInfoChangedType.NewPosterAvailable)
                    {
                        NewPosterArrivedEventArgs newPosterArgs = e as NewPosterArrivedEventArgs;
                        lock(PosterLock)
                        {
                            PosterList.Add(newPosterArgs.posterIndex, newPosterArgs.posterUrl);

                            CurrentPosterIndexBlock.Text = (PosterIndex + 1).ToString();
                            PosterCountBlock.Text = PosterList.Count.ToString();

                            if (newPosterArgs.posterIndex == PosterList.Keys[PosterIndex])
                            {
                                ImageLink.Text = newPosterArgs.posterUrl;
                                UpdateImage(null, null);
                            }
                        }
                    }
                    else if (e.InfoType == MovieInfoChangedType.AllPostersAvailable)
                    {
                        // Done loading posters.  Nothing to do for now.
                    }
                    else if (e.InfoType == MovieInfoChangedType.ImdbInfoUnavailable)
                    {
                        TitleBox.Text = "BROKEN!!";
                    }
                });
            });
        }

		private void SearchClick(object sender, System.Windows.RoutedEventArgs e)
		{
            IMDBLink.Text = "";
            TitleBox.Text = "";
            YearBox.Text = "";
            DirectorBox.Text = "";
            RunBox.Text = "";
            CodeBox.Text = "";
            GenreList.Clear();
            CastList.Clear();
            TrailerLink.Text = "";
            CurrentFreshnessIndexBlock.Text = "*";
            FreshnessCountBlock.Text = "*";
            FreshBox.Text = "";
            FreshTitle.Text = "";
            CurrentMetaScoreIndexBlock.Text = "*";
            MetaScoreCountBlock.Text = "*";
            MetaBox.Text = "";
            MetaTitle.Text = "";
            ImageLink.Text = "";
            CurrentPosterIndexBlock.Text = "*";
            PosterCountBlock.Text = "*";
            PosterList.Clear();
            PosterIndex = 0;
            Poster.Source = new System.Windows.Media.Imaging.BitmapImage(new System.Uri("http://www.reclinathon.com/movie_reel.jpg"));
            SearchTimeTextBlock.Text = "Searching...";
            Synopsis.Text = "";

            DateTime searchStart = DateTime.Now;

            XmlBox.Text = "Server Response";
			string query = SearchBox.Text;
			string IMDB = CodeBox.Text;
			
			MovieInfo m = new MovieInfo();
            m._movieInfoChanged += new MovieDatabase.MovieInfoChangedEventHandler(OnMovieInfoChanged);

            System.Object searchUiLock = new System.Object();
            bool searchInProgress = true;

            Task progressUi = Task.Run(() =>
            {
                int numDots = 3;
                while (true)
                {
                    numDots++;
                    if (numDots > 3)
                    {
                        numDots = 0;
                    }

                    lock(searchUiLock)
                    {
                        if (searchInProgress)
                        {
                            this.Dispatcher.Invoke(() => {
                                SearchTimeTextBlock.Text = "Searching";

                                for (int i = 0; i < numDots; i++)
                                {
                                    SearchTimeTextBlock.Text += ".";
                                }
                            });
                        }
                        else
                        {
                            break;
                        }
                    }                    

                    System.Threading.Thread.Sleep(500);
                }
            });

            Task updateUi = Task.Run(() => {
                m.Search(query, (sender == UpdateIMDB) ? IMDB : String.Empty);

                TimeSpan searchTime = new TimeSpan(DateTime.Now.Ticks - searchStart.Ticks);

                lock(searchUiLock)
                {
                    searchInProgress = false;
                }
                
                this.Dispatcher.Invoke(() => {
                    SearchTimeTextBlock.Text = searchTime.ToString(@"s\.fff") + " s";              
                });
            });          
		}

		private void UpdateBrowser(object sender, System.Windows.Input.MouseButtonEventArgs e)
		{
			string search = ((TextBox)sender).Text;
			if (sender == BrowserBar)
			{
				search = "http://www.bing.com/search?q="+search;
			}
			
			MiniBrowser.Source = new Uri(search);
			
			if (sender != BrowserBar)
			{
				ToggleWeb(null,null);
			}
		}

		private void UpdateImage(object sender, System.Windows.RoutedEventArgs e)
		{
			Poster.Source = new BitmapImage(new Uri(ImageLink.Text));
		}

		private void PBack(object sender, System.Windows.RoutedEventArgs e)
		{
			lock(PosterLock)
            {
                if (PosterList.Count == 0)
			    {
				    return;
			    }
			
			    if (PosterIndex == 0)
			    {
				    PosterIndex = PosterList.Count - 1;
			    }
			    else
			    {
				    PosterIndex --;
			    }

			    ImageLink.Text = PosterList[PosterIndex].ToString();
                CurrentPosterIndexBlock.Text = (PosterIndex + 1).ToString();
                UpdateImage(null,null);
            }
		}

		private void PNext(object sender, System.Windows.RoutedEventArgs e)
		{
            lock(PosterLock)
            {
                if (PosterList.Count == 0)
                {
                    return;
                }

                if (PosterIndex == PosterList.Count - 1)
                {
                    PosterIndex = 0;
                }
                else
                {
                    PosterIndex++;
                }

                ImageLink.Text = PosterList[PosterIndex].ToString();
                CurrentPosterIndexBlock.Text = (PosterIndex + 1).ToString();
                UpdateImage(null, null);
            }
		}
		
		private void FBack(object sender, System.Windows.RoutedEventArgs e)
		{
			lock(FreshnessLock)
            {
                if (FreshScoreList.Count == 0)
                {
                    return;
                }

                if (FreshnessIndex == 0)
                {
                    FreshnessIndex = FreshScoreList.Count - 1;
                }
                else
                {
                    FreshnessIndex--;
                }
                FreshBox.Text = FreshScoreList[FreshnessIndex].ToString();
                FreshTitle.Text = FreshNameList[FreshnessIndex].ToString();
                CurrentFreshnessIndexBlock.Text = (FreshnessIndex + 1).ToString();
            }
		}

		private void FNext(object sender, System.Windows.RoutedEventArgs e)
		{
			lock(FreshnessLock)
            {
                if (FreshScoreList.Count == 0)
                {
                    return;
                }

                if (FreshnessIndex == FreshScoreList.Count - 1)
                {
                    FreshnessIndex = 0;
                }
                else
                {
                    FreshnessIndex++;
                }
                FreshBox.Text = FreshScoreList[FreshnessIndex].ToString();
                FreshTitle.Text = FreshNameList[FreshnessIndex].ToString();
                CurrentFreshnessIndexBlock.Text = (FreshnessIndex + 1).ToString();
            }
		}

		private void MBack(object sender, System.Windows.RoutedEventArgs e)
		{
			lock(MetaScoreLock)
            {
                if (MetaScoreList.Count == 0)
                {
                    return;
                }

                if (MetaScoreIndex == 0)
                {
                    MetaScoreIndex = MetaScoreList.Count - 1;
                }
                else
                {
                    MetaScoreIndex--;
                }
                MetaBox.Text = MetaScoreList[MetaScoreIndex].ToString();
                MetaTitle.Text = MetaNameList[MetaScoreIndex].ToString();
                CurrentMetaScoreIndexBlock.Text = (MetaScoreIndex + 1).ToString();
            }
		}

		private void MNext(object sender, System.Windows.RoutedEventArgs e)
		{
			lock(MetaScoreLock)
            {
                if (MetaScoreList.Count == 0)
                {
                    return;
                }

                if (MetaScoreIndex == MetaScoreList.Count - 1)
                {
                    MetaScoreIndex = 0;
                }
                else
                {
                    MetaScoreIndex++;
                }
                MetaBox.Text = MetaScoreList[MetaScoreIndex].ToString();
                MetaTitle.Text = MetaNameList[MetaScoreIndex].ToString();
                CurrentMetaScoreIndexBlock.Text = (MetaScoreIndex + 1).ToString();
            }
		}
		
		private string GetXmlString()
		{
			//char[] GenreSeperator = {'|', ' '};
			//string[] Genres = GenreBox.Text.Split(GenreSeperator);
			//char[] ScoreSeperator = {'%', ',', ' '};
			//string[] Scores = FreshnessBox.Text.Split(ScoreSeperator);
			string MovieXml = "<RWSRequest><Header>";
			MovieXml += "<TimeStamp>" + System.DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss") + "</TimeStamp>";
			MovieXml += "<Season>Winter2018</Season>";
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
            MovieXml += "<Synopsis>" + Synopsis.Text + "</Synopsis>";
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
			ReclinathonWebRequest();
		}

		private void DisplayHtml(object sender, System.Windows.Input.MouseButtonEventArgs e)
		{
			MiniBrowser.NavigateToString(XmlBox.Text);
			ToggleWeb(null,null);
		}

        private void OnSearchBox(object sender, KeyEventArgs e)
        {
            if (e.Key == Key.Enter)
            {
                SearchClick(null, null);
            }
        }
    }
}