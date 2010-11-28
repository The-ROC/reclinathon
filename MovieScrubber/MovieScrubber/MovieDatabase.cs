using System;
using System.Text.RegularExpressions;
using System.Web;
using HtmlAgilityPack;

namespace MovieDatabase
{
    class MovieInfo
    {
        private HtmlAgilityPack.HtmlDocument _markup;
		private HtmlAgilityPack.HtmlDocument _markup2;
		private HtmlAgilityPack.HtmlDocument _markup3;
		private HtmlAgilityPack.HtmlDocument _markup4;
		private HtmlAgilityPack.HtmlDocument _markup5;
		private string _trailer;

        public bool Search(string query)
        {
			query = query.Replace(" ","+");
            HtmlWeb web = new HtmlWeb();
            _markup = web.Load("http://www.imdb.com/find?s=all&q=" + query);
			_markup2 = web.Load("http://www.rottentomatoes.com/movie/browser.php?title_search=" + query);
			//_markup3 = web.Load("http://apps.metacritic.com/search/process?tfs=movie_title&ts=" + query);
			_markup3 = web.Load("http://www.metacritic.com/search/movie/"+ query + "/results");
			_markup4 = web.Load("http://en.wikipedia.org/w/index.php?title=Special%3ASearch&redirs=1&search=" + query + "+poster&fulltext=Search&ns6=1");
			_markup5 = web.Load("http://www.youtube.com/results?search_query=" + query + "trailer&aq=f");
			//_trailer = "http://www.youtube.com/results?search_query=" + query + " trailer";
			_trailer = "http://www.youtube.com/results?search_filter=1&search_type=videos&suggested_categories=1%2C24&uni=3&partner=1&search_query=" + query + "+trailer";
			
            if (!IsMoviePage())
            {
                string urlBestResult = FindBestMatch();
                if (!String.IsNullOrEmpty(urlBestResult))
                {
                    _markup = web.Load(urlBestResult);
                }
                else return false;
            }

            return IsMoviePage();
        }
		
		public bool Search(string query, string IMDB)
        {
			query = query.Replace(" ","+");
            HtmlWeb web = new HtmlWeb();
            //_markup = web.Load("http://www.imdb.com/title/tt" + IMDB);
			_markup = web.Load(IMDB);
			_markup2 = web.Load("http://www.rottentomatoes.com/movie/browser.php?title_search=" + query);
			//_markup3 = web.Load("http://apps.metacritic.com/search/process?tfs=movie_title&ts=" + query);
			_markup3 = web.Load("http://www.metacritic.com/search/movie/"+ query + "/results");
			_markup4 = web.Load("http://en.wikipedia.org/w/index.php?title=Special%3ASearch&redirs=1&search=" + query + "+poster&fulltext=Search&ns6=1");
			_markup5 = web.Load("http://www.youtube.com/results?search_query=" + query + "trailer&aq=f");
			//_trailer = "http://www.youtube.com/results?search_query=" + query + " trailer";
			_trailer = "http://www.youtube.com/results?search_filter=1&search_type=videos&suggested_categories=1%2C24&uni=3&partner=1&search_query=" + query + "+trailer";
			
			/*
            if (!IsMoviePage())
            {
                string urlBestResult = FindBestMatch();
                if (!String.IsNullOrEmpty(urlBestResult))
                {
                    return false;//_markup = web.Load(urlBestResult);
                }
                else return false;
            }

            return IsMoviePage();
			*/
			return IsMoviePage();
        }


        private bool IsMoviePage()
        {
            //return (_markup.DocumentNode.SelectSingleNode("//h3[contains(., 'Overview')]") != null);
			return (_markup.DocumentNode.SelectSingleNode("//div[contains(@class, 'infobar')]") != null);
        }


        private string FindBestMatch()
        {
            HtmlNodeCollection headers = _markup.DocumentNode.SelectNodes("//p/b[contains(., 'Titles')]");
            if (headers != null) foreach (HtmlNode header in headers)
            {
                HtmlNode link = header.ParentNode.SelectSingleNode(".//a[contains(@href, '/title/')]");
                if (link != null)
                {
                    return "http://www.imdb.com" + link.Attributes["href"].Value;
                }
            }
            return String.Empty;
        }


        public string Title
        {
            get
            {
                HtmlNode title = _markup.DocumentNode.SelectSingleNode("//title");
                return title == null ? String.Empty : title.InnerText.Remove(title.InnerText.IndexOf("(")).Replace("&#x22;", String.Empty);
            }
        }


        public string Year
        {
            get
            {
                HtmlNode year = _markup.DocumentNode.SelectSingleNode("//title");
                return year == null ? String.Empty : Regex.Match(year.InnerText, @"\((.*?)\)").Groups[1].Value;
            }
        }


        public string Link
        {
            get
            {
                HtmlNode link = _markup.DocumentNode.SelectSingleNode("//link[contains(@rel, 'canonical')]");
                return link == null ? String.Empty : link.Attributes["href"].Value;
            }
        }


        public string Id
        {
            get
            {
                return this.Link.Replace("http://www.imdb.com/title/tt", String.Empty).TrimEnd('/');
            }
        }


        public string Director
        {
            get
            {
				HtmlNodeCollection nodes = _markup.DocumentNode.SelectNodes("//h4[contains(@class, 'inline')]");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					if (node.InnerText.Contains("Director"))
					{
						HtmlNode dirname = node.ParentNode.SelectSingleNode(".//a");
						return dirname == null ? String.Empty : dirname.InnerText;
					}
				}
					
                return String.Empty;
            }
        }


        public string Genre
        {
            get
            {
                String genres = "";
				HtmlNodeCollection nodes = _markup.DocumentNode.SelectNodes("//h4[contains(@class, 'inline')]");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					if (node.InnerText.Contains("Genre"))
					{
						foreach (HtmlNode genrenode in node.ParentNode.Elements("a"))
						{
							genres += genrenode.InnerText + "|";
						}
						return genres;
					}
				}
					
                return String.Empty;
				/*
				HtmlNode header = _markup.DocumentNode.SelectSingleNode("//div[contains(@class, 'info')]/h5[contains(., 'Genre:')]");
                if (header != null)
                {
                    HtmlNode genre = header.ParentNode.SelectSingleNode(".//div[contains(@class, 'info-content')]");
                    if (genre != null) return genre.InnerText.Replace("See more&nbsp;&raquo;", String.Empty).Trim();
                }
                return String.Empty;
				//HtmlNodeCollection junk = new HtmlNodeCollection();
				//junk.Elements*/
            }
        }
		
		public string Cast
		{
			get
			{
				String actors = "";
				HtmlNodeCollection nodes = _markup.DocumentNode.SelectNodes("//td[contains(@class, 'name')]");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					actors += node.InnerText.Trim() + "|";
				}
					
				return actors;

			}
				
				/*
				string actors = String.Empty;
				HtmlNode header = _markup.DocumentNode.SelectSingleNode("//div[contains(@class, 'info')]/div/h3[contains(., 'Cast')]");
                if (header != null)
                {
					//HtmlNode cast = header.ParentNode.ParentNode.SelectSingleNode(".//div[contains(@class, 'info-content block')]/table/tr/td[contains(@class,'nm')]");
					//if (cast != null){return cast.InnerText;}
					//string broke = "Broken!";
					//return broke;
					//HtmlNode cast = header.ParentNode.ParentNode.SelectSingleNode(".//div[contains(@class, 'info-content block')]");
					HtmlNodeCollection cast = header.ParentNode.ParentNode.SelectNodes(".//div[contains(@class, 'info-content block')]/table/tr/td[contains(@class,'nm')]");
					if (cast != null)
					{
						foreach (HtmlNode c in cast)
						{
							if (c != null) 
							{
								if (actors == String.Empty) {actors = actors + c.InnerText;}
								else {actors =  actors + " | " + c.InnerText;}
							}
						}
						//return actors;
					}
					actors = actors.Replace("&#x27;","'").Replace("&#xE9;","e").Replace("&#xE1;","a").Replace("&#xF1;","n");
					return actors;
				}
				return String.Empty;
				*/
		}


        public string Plot
        {
            get
            {
                HtmlNode header = _markup.DocumentNode.SelectSingleNode("//div[contains(@class, 'info')]/h5[contains(., 'Plot:')]");
                if (header != null)
                {
                    HtmlNode plot = header.ParentNode.SelectSingleNode(".//div[contains(@class, 'info-content')]");
                    if (plot != null) return plot.InnerText.Replace("Full summary&nbsp;&raquo;", String.Empty).Trim();
                }
                return String.Empty;
            }
        }
		
		
		public string Runtime
        {
			get
            {
				HtmlNodeCollection nodes = _markup.DocumentNode.SelectNodes("//h4[contains(@class, 'inline')]");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					if (node.InnerText.Contains("Runtime"))
					{
						HtmlNode runtime = node.ParentNode; 
						string rt = runtime == null ? String.Empty : runtime.InnerText;
						string target = "0123456789";
   						char[] anyOf = target.ToCharArray();
						int at = rt.IndexOfAny(anyOf);
						int lastat = rt.LastIndexOfAny(anyOf);
						rt = rt.Substring(at, lastat - at + 1);
						return rt;
					}
				}
					
                return String.Empty;
            }
        
        }
		
		public string Freshness
        {
            get
            {
				string fscore = "";
				HtmlNodeCollection nodes = _markup2.DocumentNode.SelectNodes("//td[contains(@class, 'firstCol tomatometer')]/p/strong");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					fscore += node.InnerText.Trim().Trim('%') + "|";
				}
					
				return fscore;
                /*
				HtmlNode fscore = _markup2.DocumentNode.SelectSingleNode("//td[contains(@class, 'firstCol tomatometer')]/p/span/span");
                if (fscore != null)
                {
                    return fscore.InnerText;
                }
                return String.Empty;
				*/
            }
        }
		
		public string FreshTitle
		{
			get
			{	
				string ftitle = "";
				HtmlNodeCollection nodes = _markup2.DocumentNode.SelectNodes("//tr[contains(@class, 'e8edf5') or contains(@class, 'alt')]/td/p/strong/a");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					ftitle += node.InnerText.Trim() + "|";
				}
				
				return ftitle;
			}
				/*
				HtmlNode ftitle = _markup2.DocumentNode.SelectSingleNode("//tr[contains(@class, 'e8edf5')]/td/p/strong/a");
				if (ftitle != null)
				{
					return ftitle.InnerText;
				}
				return "No Match Found!";
				*/
		}
		
		public string MetaScore
        {
            get
            {
				string mscore = "";
				
				HtmlNodeCollection nodes = _markup3.DocumentNode.SelectNodes("//span[contains(@class,'data metascore')]");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					string ms = node.InnerText;
					/*HtmlNode mscore2 = node.ParentNode;
                    string ms = mscore2 == null ? String.Empty : mscore2.InnerText;
					string target = "0123456789";
   					char[] anyOf = target.ToCharArray();
					int at = ms.IndexOfAny(anyOf);
					int lastat = ms.LastIndexOfAny(anyOf);
					ms = ms.Substring(at, lastat - at + 1);*/
					mscore += ms + "|";
				}
				
				return mscore;
				
				/*
                HtmlNode mscore = _markup3.DocumentNode.SelectSingleNode("//div[contains(@class,'score_wrap')]/span");//("//span[contains(@class, 'green') or contains(@class, 'yellow') or contains(@class, 'red')]");
                if (mscore != null)
                {
					HtmlNode mscore2 = mscore.ParentNode;
                    string ms = mscore2 == null ? String.Empty : mscore2.InnerText;
					string target = "0123456789";
   					char[] anyOf = target.ToCharArray();
					int at = ms.IndexOfAny(anyOf);
					int lastat = ms.LastIndexOfAny(anyOf);
					ms = ms.Substring(at, lastat - at + 1);
					return ms;
					
                }
                return "N/R";
				*/
            }
        }
		
		public string MetaTitle
		{
			get
			{
				string mtitle = "";
				HtmlNodeCollection nodes = _markup3.DocumentNode.SelectNodes("//div[contains(@class, 'main_stats')]/h3/a");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					mtitle += node.InnerText.Trim() + "|";
				}
				
				return mtitle;
				/*
				HtmlNode mtitle = _markup3.DocumentNode.SelectSingleNode("//div[contains(@class, 'main_stats')]/h3/a");//("//a[contains(@href, 'http://www.metacritic.com')]/b");
				if (mtitle != null)
				{
					return mtitle.InnerText;
				}
				return "No Match Found!";
				*/
			}
		}
		
		public string PosterLink
		{
			get
			{
				String posters = "";
				HtmlNodeCollection nodes = _markup4.DocumentNode.SelectNodes("//ul[contains(@class, 'mw-search-results')]/li/table/tr/td/a");
				if (nodes != null) foreach (HtmlNode node in nodes)
				{
					string lookup = "http://en.wikipedia.org" + node.Attributes["href"].Value;
					HtmlWeb poster_web = new HtmlWeb();
					HtmlAgilityPack.HtmlDocument _pmarkup = poster_web.Load(lookup);
					
					HtmlNode poster = _pmarkup.DocumentNode.SelectSingleNode("//div[contains(@class, 'fullImageLink')]/a");
					if (poster != null)
					{
						posters = posters + poster.Attributes["href"].Value + "|";
					}
				}
					return posters;
			}
		}	
			/*	
			HtmlNode plink = _markup4.DocumentNode.SelectSingleNode("//ul[contains(@class, 'mw-search-results')]/li/table/tr/td/a");
				if (plink != null)
				{
					//return "http://en.wikipedia.org" + plink.Attributes["href"].Value;	
					string lookup = "http://en.wikipedia.org" + plink.Attributes["href"].Value;
					HtmlWeb poster_web = new HtmlWeb();
					HtmlAgilityPack.HtmlDocument _pmarkup = poster_web.Load(lookup);
					
					HtmlNode poster = _pmarkup.DocumentNode.SelectSingleNode("//div[contains(@class, 'fullImageLink')]/a");
					if (poster != null)
					{
						return poster.Attributes["href"].Value;
						//string address =  poster.Attributes["href"].Value;
						//return address;
					}
					return String.Empty;
				}
				return String.Empty;
			}
		}*/
		
		/*public System.Windows.Media.Imaging.BitmapImage PosterImage
		{
			get
			{
				HtmlNode plink = _markup4.DocumentNode.SelectSingleNode("//ul[contains(@class, 'mw-search-results')]/li/table/tr/td/a");
				if (plink != null)
				{
					string lookup = "http://en.wikipedia.org" + plink.Attributes["href"].Value;
				
					HtmlWeb poster_web = new HtmlWeb();
					HtmlAgilityPack.HtmlDocument _pmarkup = poster_web.Load(lookup);
				
					HtmlNode poster = _pmarkup.DocumentNode.SelectSingleNode("//div[contains(@class, 'fullImageLink')]/a");
					if (poster != null)
					{
						string address =  poster.Attributes["href"].Value;
						return new System.Windows.Media.Imaging.BitmapImage(new System.Uri(address));
					}
					return new System.Windows.Media.Imaging.BitmapImage(new System.Uri(@"C:\Users\David\Pictures\movie_reel.jpg"));
				}
				return new System.Windows.Media.Imaging.BitmapImage(new System.Uri(@"C:\Users\David\Pictures\movie_reel.jpg"));
			}
		}*/
		
		public string TrailerLink
		{
			get
			{
				return _trailer;
			}
		}
			
    }
}
