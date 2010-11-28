using System;
using System.Text.RegularExpressions;
using System.Web;
using HtmlAgilityPack;

namespace SearchImdb
{
    class Imdb
    {
        private HtmlAgilityPack.HtmlDocument _markup;


        public bool Search(string query)
        {
            HtmlWeb web = new HtmlWeb();
            _markup = web.Load("http://www.imdb.com/find?s=all&q=" + query);

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


        private bool IsMoviePage()
        {
            return (_markup.DocumentNode.SelectSingleNode("//h3[contains(., 'Overview')]") != null);
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
                HtmlNode director = _markup.DocumentNode.SelectSingleNode("//div[contains(@id, 'director-info')]/div/a[@href]");
                return director == null ? String.Empty : director.InnerText;
            }
        }


        public string Genre
        {
            get
            {
                HtmlNode header = _markup.DocumentNode.SelectSingleNode("//div[contains(@class, 'info')]/h5[contains(., 'Genre:')]");
                if (header != null)
                {
                    HtmlNode genre = header.ParentNode.SelectSingleNode(".//div[contains(@class, 'info-content')]");
                    if (genre != null) return genre.InnerText.Replace("See more&nbsp;&raquo;", String.Empty).Trim();
                }
                return String.Empty;
            }
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
    }
}
