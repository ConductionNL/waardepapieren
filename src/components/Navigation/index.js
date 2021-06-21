import React from 'react'
import { Link, useStaticQuery, graphql } from 'gatsby'
import { getCurrentLangKey, getUrlForLang } from 'ptz-i18n'

import { StyledNav, StyledTitleLink, StyledTitle, StyledList, StyledListItem } from './index.styles'
import DemodamLogo from '../DemodamLogo'
import GithubLogo from '../GithubLogo'
import SlackLogo from '../SlackLogo'
import LanguageSelector from '../LanguageSelector'
import menu from '../../pages/menu.json'
import dimpact from "../Logos/images/dimpact.png";

const Navigation = ({ location }) => {
  const { site } = useStaticQuery(
    graphql`
      query {
        site {
          siteMetadata {
            title
            repositoryUrl
            slackUrl
            languages
            defaultLanguage
          }
        }
      }
    `
  )

  const langKey = getCurrentLangKey(
    site.siteMetadata.languages,
    site.siteMetadata.defaultLanguage,
    location ? location.pathname : '/'
  )

  const homeLink = `/${langKey}/`.replace(`/${site.siteMetadata.defaultLanguage}/`, '/')

  const languageSelector = site.siteMetadata.languages.map(languageKey => {
    return {
      languageKey: languageKey,
      link: getUrlForLang(homeLink, location.pathname, languageKey).replace(`/${site.siteMetadata.defaultLanguage}/`, '/')
    }
  })

  return (
    <StyledNav>
      <StyledTitleLink to={homeLink}>
        <img src={dimpact} alt={"logo van dimpact"}/>
        <StyledTitle>

        </StyledTitle>
      </StyledTitleLink>
      <StyledList>
        {menu[langKey].map((item, i) => (
          <StyledListItem key={i}>
            <Link to={item.link} activeClassName="active" partiallyActive={item.partiallyActive}>{item.title}</Link>
          </StyledListItem>
        ))}
        <StyledListItem>
          <a href={"https://github.com/ConductionNL/waardepapieren"} target="_blank" rel="noopener noreferrer" aria-label="Bekijk project op Github">
            <GithubLogo  width="26px" height="24px" />
          </a>
          <a href={"samenorganiseren.slack.com"} target="_blank" rel="noopener noreferrer" aria-label="Wordt lid van het Waardenpapieren kanaal op de Common Ground Slack">
            <SlackLogo width="26px" />
          </a>
        </StyledListItem>
        <StyledListItem>
          <LanguageSelector languages={languageSelector} />
        </StyledListItem>
      </StyledList>
    </StyledNav>
  )
}

export default Navigation
