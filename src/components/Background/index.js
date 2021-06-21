import styled from 'styled-components/macro'

const Background = styled.div`
  background-color: ${(p) => p.backgroundColor || "#ffffff"};
  color: ${(p) => p.color || "#FA4494"};

  a {
    color: ${(p) => p.color || "#FA4494"};
  }
`

export default Background
