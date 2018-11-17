using System;
using System.Text.RegularExpressions;
using System.Web;
using System.Threading.Tasks;
using System.Collections.Generic;
using System.Collections;
using HtmlAgilityPack;
using Newtonsoft.Json.Linq;

namespace MovieDatabase
{
    public enum MovieInfoChangedType
    {
        ImdbInfoAvailable,
        ImdbInfoUnavailable,
        TrailerInfoAvailable,
        RottenTomatoesInfoAvailable,
        MetaCriticInfoAvailable,
        NewPosterAvailable,
        AllPostersAvailable
    }
    public class MovieInfoChangedEventArgs : EventArgs
    {
        public MovieInfoChangedType InfoType { get; set; }        
    }

    public class GenericMovieInfoChangedEventArgs : MovieInfoChangedEventArgs
    {
        public GenericMovieInfoChangedEventArgs(MovieInfoChangedType type)
        {
            InfoType = type;
        }
    }

    public class SingleStringMovieInfoEventArgs : MovieInfoChangedEventArgs
    {
        public string infoString { get; set; }
        public SingleStringMovieInfoEventArgs(MovieInfoChangedType type, string data)
        {
            InfoType = type;
            infoString = data;
        }
    }

    public class MovieDetailsEventArgs : MovieInfoChangedEventArgs
    {
        public string imdbLink { get; set; }
        public string imdbId { get; set; }
        public string movieTitle { get; set; }
        public string releaseYear { get; set; }
        public string directorName { get; set; }
        public string movieRuntime { get; set; }
        public List<string> castList { get; set; }
        public List<string> genreList { get; set; }
        public string synopsisText { get; set; }

        public MovieDetailsEventArgs(string link, string id, string title, string year, string director, string runtime, List<string> cast, List<string> genres, string synopsis)
        {
            InfoType = MovieInfoChangedType.ImdbInfoAvailable;
            imdbLink = link;
            imdbId = id;
            movieTitle = title;
            releaseYear = year;
            directorName = director;
            movieRuntime = runtime;
            castList = cast;
            genreList = genres;
            synopsisText = synopsis;
        }
    }

    public class NewPosterArrivedEventArgs : MovieInfoChangedEventArgs
    {
        public int posterIndex { get; set; }
        public string posterUrl { get; set; }

        public NewPosterArrivedEventArgs(int index, string url)
        {
            InfoType = MovieInfoChangedType.NewPosterAvailable;
            posterIndex = index;
            posterUrl = url;
        }
    }

    public class MovieScoreEventArgs : MovieInfoChangedEventArgs
    {
        public Dictionary<string, string> movieScoreList { get; set; }

        public MovieScoreEventArgs(MovieInfoChangedType type, Dictionary<string, string> scoreList)
        {
            InfoType = type;
            movieScoreList = scoreList;
        }
    }

    public delegate void MovieInfoChangedEventHandler(object sender, MovieInfoChangedEventArgs e);

    class MovieInfo
    {
        private TimeSpan _loadAll;
        private TimeSpan _loadImdb;
        private TimeSpan _loadImdbBestMatch;
        private TimeSpan _loadRottenTomatoes;
        private TimeSpan _loadMetacritic;
        private TimeSpan _loadWikipedia;
        private TimeSpan _loadPosters;
       
        public event MovieInfoChangedEventHandler _movieInfoChanged;

        public virtual void OnMovieInfoChanged(MovieInfoChangedEventArgs e)
        {
            if (_movieInfoChanged != null)
            {
                _movieInfoChanged(this, e);
            }
        }

        public void Search(string query, string imdbId)
        {
			query = query.Replace(" ","+");
            HtmlWeb web = new HtmlWeb();

            DateTime loadStart = DateTime.Now;

            List<Task> tasks = new List<Task>();

            tasks.Add(Task.Run(() => {
                DateTime loadImdbStart = DateTime.Now;

                string imdbUrl = (imdbId != String.Empty) ? "http://www.imdb.com/title/tt" + imdbId : "http://www.imdb.com/find?s=all&q=" + query;
                HtmlAgilityPack.HtmlDocument imdbPage = web.Load(imdbUrl);

                _loadImdb = new TimeSpan(DateTime.Now.Ticks - loadImdbStart.Ticks);

                if (imdbId == String.Empty && !IsMoviePage(imdbPage))
                {
                    string urlBestResult = FindBestMatch(imdbPage);
                    if (!String.IsNullOrEmpty(urlBestResult))
                    {
                        DateTime loadImdbBestMatchStart = DateTime.Now;
                        imdbPage = web.Load(urlBestResult);
                        _loadImdbBestMatch = new TimeSpan(DateTime.Now.Ticks - loadImdbBestMatchStart.Ticks);
                    }
                }

                if (!IsMoviePage(imdbPage))
                {
                    OnMovieInfoChanged(new GenericMovieInfoChangedEventArgs(MovieInfoChangedType.ImdbInfoUnavailable));
                    return;
                }

                HtmlNode titleNode = imdbPage.DocumentNode.SelectSingleNode("//title");
                HtmlNode yearNode = imdbPage.DocumentNode.SelectSingleNode("//title");
                HtmlNode linkNode = imdbPage.DocumentNode.SelectSingleNode("//link[contains(@rel, 'canonical')]");
                HtmlNodeCollection movieInfoNodes = imdbPage.DocumentNode.SelectNodes("//h4[contains(@class, 'inline')]");
                HtmlNodeCollection castNodes = imdbPage.DocumentNode.SelectNodes("//td[contains(@class, 'primary_photo')]/a/img");
                HtmlNode synopsisNode = imdbPage.DocumentNode.SelectSingleNode("//div[contains(@class, 'summary_text')]");

                string title = (titleNode == null) ? String.Empty : titleNode.InnerText.Remove(titleNode.InnerText.IndexOf("(")).Replace("&#x22;", String.Empty);
                string releaseYear = (yearNode == null) ? String.Empty : Regex.Match(yearNode.InnerText, @"\((.*?)\)").Groups[1].Value;
                string imdbLink = (linkNode == null) ? String.Empty : linkNode.Attributes["href"].Value;
                imdbId = imdbLink.Replace("http://www.imdb.com/title/tt", String.Empty).TrimEnd('/');
                string director = String.Empty;
                List<string> genreList = new List<string>();
                string runtime = String.Empty;
                List<string> castList = new List<string>();
                string synopsis = (synopsisNode == null) ? String.Empty : synopsisNode.InnerText.Trim();

                if (movieInfoNodes != null)
                {
                    foreach (HtmlNode node in movieInfoNodes)
                    {
                        if (director == String.Empty && node.InnerText.Contains("Director"))
                        {
                            HtmlNode directorNameNode = node.ParentNode.SelectSingleNode(".//a");
                            director = (directorNameNode == null) ? String.Empty : directorNameNode.InnerText.Trim();
                        }
                        else if (node.InnerText.Contains("Genre"))
                        {
                            foreach (HtmlNode genreNode in node.ParentNode.Elements("a"))
                            {
                                genreList.Add(genreNode.InnerText);
                            }
                        }
                        else if (runtime == String.Empty && node.InnerText.Contains("Runtime"))
                        {
                            HtmlNode runtimeNode = node.ParentNode;
                            runtime = (runtimeNode == null) ? String.Empty : runtimeNode.InnerText;
                            string target = "0123456789";
                            char[] anyOf = target.ToCharArray();
                            int at = runtime.IndexOfAny(anyOf);
                            int lastat = runtime.LastIndexOfAny(anyOf);
                            runtime = runtime.Substring(at, lastat - at + 1);
                        }
                    }
                }

                if (castNodes != null)
                foreach (HtmlNode node in castNodes)
                {
                    castList.Add(node.Attributes["title"].Value.Trim());
                }

                OnMovieInfoChanged(new MovieDetailsEventArgs(imdbLink, imdbId, title, releaseYear, director, runtime, castList, genreList, synopsis));
            }));

            tasks.Add(Task.Run(() => {
                DateTime loadRottenTomatoesStart = DateTime.Now;
                HtmlAgilityPack.HtmlDocument rottenTomatoesPage = web.Load("http://www.rottentomatoes.com/search/?search=" + query);
                _loadRottenTomatoes = new TimeSpan(DateTime.Now.Ticks - loadRottenTomatoesStart.Ticks);

                Dictionary<string, string> freshnessScores = new Dictionary<string, string>();

                HtmlNode moviePageNode = rottenTomatoesPage.DocumentNode.SelectSingleNode("//div[contains(@id, 'mainColumn')]");
                if (moviePageNode != null)
                {
                    HtmlNode freshnessScoreNode = moviePageNode.SelectSingleNode("//span[contains(@itemprop, 'ratingValue')]");
                    HtmlNode titleNode = moviePageNode.SelectSingleNode("h1[contains(@itemprop, 'name')]");

                    if (freshnessScoreNode != null && titleNode != null)
                    {
                        string freshnessScore = freshnessScoreNode.InnerText.Trim();
                        string title = titleNode.InnerText.Trim();

                        if (!freshnessScores.ContainsKey(title))
                        {
                            freshnessScores.Add(title, freshnessScore);
                        }
                    }
                }
                else
                {
                    HtmlNode resultsDivSiblingNode = rottenTomatoesPage.DocumentNode.SelectSingleNode("//div[contains(@id, 'search-results-root')]");
                    HtmlNode resultsScriptNode = null;

                    if (resultsDivSiblingNode != null && resultsDivSiblingNode.ParentNode != null)
                    {
                        resultsScriptNode = resultsDivSiblingNode.ParentNode.SelectSingleNode("script");
                    }

                    if (resultsScriptNode != null)
                    {
                        String resultsScript = resultsScriptNode.InnerText.Trim();
                        int jsonStartIndex = resultsScript.IndexOf("{", resultsScript.IndexOf("search-results-root"));
                        int jsonEndIndex = resultsScript.LastIndexOf("}", resultsScript.LastIndexOf("}") - 1);
                        String resultsJsonString = resultsScript.Substring(jsonStartIndex, jsonEndIndex - jsonStartIndex + 1);

                        JObject resultsJson = JObject.Parse(resultsJsonString);

                        if (resultsJson != null && resultsJson["movies"] != null)
                        {
                            foreach (JToken movie in resultsJson["movies"])
                            {
                                String title = (String)movie["name"];
                                String year = (String)movie["year"];
                                String freshness = (String)movie["meterScore"];

                                if (title != null && year != null && freshness != null)
                                {
                                    string freshnessTitle = title + " " + year;
                                    freshnessScores.Add(freshnessTitle, freshness);
                                }
                            }
                        }
                    }
                }                

                OnMovieInfoChanged(new MovieScoreEventArgs(MovieInfoChangedType.RottenTomatoesInfoAvailable, freshnessScores));
            }));

            tasks.Add(Task.Run(() => {
                DateTime loadMetacriticStart = DateTime.Now;
                HtmlAgilityPack.HtmlDocument metaCriticPage = web.Load("http://www.metacritic.com/search/movie/" + query + "/results");
                _loadMetacritic = new TimeSpan(DateTime.Now.Ticks - loadMetacriticStart.Ticks);

                Dictionary<string, string> metaScores = new Dictionary<string, string>();
                HtmlNodeCollection movieNodes = metaCriticPage.DocumentNode.SelectNodes("//div[contains(@class, 'main_stats')]");
                if (movieNodes != null)
                {
                    foreach (HtmlNode node in movieNodes)
                    {
                        HtmlNode metaScoreNode = node.SelectSingleNode("span");
                        HtmlNode titleNode = node.SelectSingleNode("h3/a");
                        HtmlNode releaseDateNode = node.ParentNode.SelectSingleNode("div/ul/li[contains(@class, 'stat release_date')]/span[contains(@class, 'data')]");

                        if (metaScoreNode != null && titleNode != null)
                        {
                            string metaScore = metaScoreNode.InnerText.Trim();
                            string title = titleNode.InnerText.Trim();

                            if (releaseDateNode != null)
                            {
                                title += " (" + releaseDateNode.InnerText.Trim() + ")";
                            }

                            if (!metaScores.ContainsKey(title))
                            {
                                metaScores.Add(title, metaScore);
                            }
                        }
                    }
                }

                OnMovieInfoChanged(new MovieScoreEventArgs(MovieInfoChangedType.MetaCriticInfoAvailable, metaScores));
            }));

            tasks.Add(Task.Run(() => {
                DateTime loadWikipediaStart = DateTime.Now;
                HtmlAgilityPack.HtmlDocument wikipediaPage = web.Load("http://en.wikipedia.org/w/index.php?title=Special%3ASearch&profile=images&search=" + query + "+poster&fulltext=Search");
                
                List<Task> posterTasks = new List<Task>();
                HtmlNodeCollection nodes = wikipediaPage.DocumentNode.SelectNodes("//ul[contains(@class, 'mw-search-results')]/li/table/tr/td/a[contains(@class, 'image')]");
                if (nodes != null)
                {
                    int postersAdded = 0;
                    HtmlWeb poster_web = new HtmlWeb();
                    foreach (HtmlNode node in nodes)
                    {
                        if (!IsAcceptableImageType(node.Attributes["href"].Value))
                        {
                            continue;
                        }

                        string lookup = "http://en.wikipedia.org" + node.Attributes["href"].Value;

                        Action<object> loadPosterAction = i =>
                        {
                            DateTime loadPosterStart = DateTime.Now;
                            HtmlAgilityPack.HtmlDocument _pmarkup = web.Load(lookup);
                            _loadPosters = _loadPosters.Add(new TimeSpan(DateTime.Now.Ticks - loadPosterStart.Ticks));

                            HtmlNode poster = _pmarkup.DocumentNode.SelectSingleNode("//div[contains(@class, 'fullImageLink')]/a");
                            if (poster != null)
                            {
                                OnMovieInfoChanged(new NewPosterArrivedEventArgs((int)i, "http:" + poster.Attributes["href"].Value));
                            }
                        };

                        Task loadPosterTask = Task.Factory.StartNew(loadPosterAction, postersAdded);

                        posterTasks.Add(loadPosterTask);

                        postersAdded++;
                        if (postersAdded > 20)
                        {
                            break;
                        }
                    }
                }

                Task.WaitAll(posterTasks.ToArray());

                _loadWikipedia = new TimeSpan(DateTime.Now.Ticks - loadWikipediaStart.Ticks);

                OnMovieInfoChanged(new GenericMovieInfoChangedEventArgs(MovieInfoChangedType.AllPostersAvailable));
            }));

            string trailerLink = "http://www.youtube.com/results?search_query=" + query + "+trailer%2C+hd%2C+short";
            OnMovieInfoChanged(new SingleStringMovieInfoEventArgs(MovieInfoChangedType.TrailerInfoAvailable, trailerLink));

            Task.WaitAll(tasks.ToArray());

            _loadAll = new TimeSpan(DateTime.Now.Ticks - loadStart.Ticks);
        }

        private bool IsAcceptableImageType(string source)
        {
            if (source.EndsWith(".jpg") ||
                source.EndsWith(".png") ||
                source.EndsWith(".jpef") ||
                source.EndsWith(".gif") ||
                source.EndsWith(".bmp"))
            {
                return true;
            }

            return false;
        }

        private bool IsMoviePage(HtmlAgilityPack.HtmlDocument imdbPage)
        {
            return (imdbPage.DocumentNode.SelectSingleNode("//div[contains(@class, 'infobar')]") != null) || 
                   (imdbPage.DocumentNode.SelectSingleNode("//div[contains(@class, 'titleBar')]") != null);
        }


        private string FindBestMatch(HtmlAgilityPack.HtmlDocument imdbPage)
        {
            HtmlNodeCollection headers = imdbPage.DocumentNode.SelectNodes("//td[contains(@class, 'result_text')]");
            if (headers != null)
            {
                foreach (HtmlNode header in headers)
                {
                    HtmlNode link = header.SelectSingleNode(".//a[contains(@href, '/title/tt')]");
                    if (link != null)
                    {
                        return "http://www.imdb.com" + link.Attributes["href"].Value;
                    }
                }
            }
            return String.Empty;
        }					
    }
}
